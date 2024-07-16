<?php

namespace App\Http\GarminClient;

use App\Http\GarminClient\GarminAuthenticator;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GarminClient
{
    public const GARMIN_API_URL = 'https://connectapi.garmin.com';

    public const USER_AGENT = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148';

    public function __construct(
        private HttpClientInterface $client,
        private readonly GarminAuthenticator $garminAuthenticator
    ) {
    }

    public function addCredentials(string $username, string $password): void
    {
        $this->garminAuthenticator->setGarminUsername($username);
        $this->garminAuthenticator->setGarminPassword($password);
    }

    public function setup(): void
    {
        $accessToken = $this->garminAuthenticator->authenticate();

        $this->client = $this->client->withOptions(
            (new HttpOptions())
                ->setHeaders([
                    'User-Agent' => self::USER_AGENT,
                    'Authorization' => 'Bearer ' . $accessToken,
                ])
                ->toArray()
        );
    }

    public function fetchPersonalInformation(): array
    {
        $this->setup();
        $response = $this->client->request('GET', self::GARMIN_API_URL . '/userprofile-service/userprofile/personal-information');

        return $response->toArray();
    }

    public function getWorkoutList($intStart = 0, $intLimit = 10, $myWorkoutsOnly = true, $sharedWorkoutsOnly = false)
    {
        $this->setup();
        $queryParameters = ['start' => $intStart, 'limit' => $intLimit, 'myWorkoutsOnly' => $myWorkoutsOnly, 'sharedWorkoutsOnly' => $sharedWorkoutsOnly];

        $client = $this->client->withOptions(
            (new HttpOptions())
                ->setQuery($queryParameters)
                ->toArray()
        );

        $response = $client->request(
            'GET',
            self::GARMIN_API_URL . '/workout-service/workouts'
        );

        if ($response->getStatusCode() != 200) {
            throw new ClientException('Response code - ' . $response->getStatusCode());
        }

        $objResponse = json_decode($response->getContent());
        return $objResponse;
    }

    public function createWorkout($data)
    {
        $this->setup();
        if (empty($data)) {
            throw new DataException('Data must be supplied to create a new workout.');
        }

        $headers = [
            'NK: NT',
            'Content-Type: application/json'
        ];

        $client = $this->client->withOptions(
            (new HttpOptions())
                ->setJson($data)
                ->setHeaders($headers)
                ->toArray()
        );

        $response = $client->request(
            'POST',
            self::GARMIN_API_URL . '/workout-service/workout'
        );

        if ($response->getStatusCode() != 200) {
            throw new ClientException('Response code - ' . $response->getStatusCode());
        }

        $objResponse = json_decode($response->getContent());
        return $objResponse;
    }

    /**
     * Delete a workout based upon the workout ID
     *
     * @param $id
     * @return mixed
     * @throws ClientException
     */
    public function deleteWorkout(?string $id)
    {
        $this->setup();
        if (empty($id)) {
            throw new DataException('Workout ID must be supplied to delete a workout.');
        }

        $headers = [
            'NK: NT',
            'X-HTTP-Method-Override: DELETE'
        ];

        $client = $this->client->withOptions(
            (new HttpOptions())
                ->setHeaders($headers)
                ->toArray()
        );

        $response = $client->request(
            'POST',
            self::GARMIN_API_URL . '/workout-service/workout/' . $id
        );

        if ($response->getStatusCode() != 204) {
            throw new ClientException('Response code - ' . $response->getStatusCode());
        }

        $objResponse = json_decode($response->getContent());
        return $objResponse;
    }

    /**
     * Schedule a workout on the calendar
     *
     * @param $id
     * @param $payload
     * @return mixed
     * @throws ClientException
     */
    public function scheduleWorkout(?string $id, $data)
    {
        $this->setup();
        $headers = [
            'NK: NT',
            'Content-Type: application/json'
        ];

        if (empty($id)) {
            throw new DataException('Workout ID must be supplied to delete a workout.');
        }

        $client = $this->client->withOptions(
            (new HttpOptions())
                ->setJson($data)
                ->setHeaders($headers)
                ->toArray()
        );

        $response = $client->request(
            'POST',
            self::GARMIN_API_URL . '/workout-service/schedule/' . $id
        );

        if ($response->getStatusCode() != 200) {
            throw new ClientException('Response code - ' . $response->getStatusCode());
        }

        $objResponse = json_decode($response->getContent());
        return $objResponse;
    }
}
