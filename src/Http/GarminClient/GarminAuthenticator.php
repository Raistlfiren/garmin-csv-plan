<?php

namespace App\Http\GarminClient;

use App\Http\OauthHttpDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GarminAuthenticator
{
    public const GARTH_SSO_TOKENS = 'https://thegarth.s3.amazonaws.com/oauth_consumer.json';
    public const GARMIN_SSO_URL = 'https://sso.garmin.com/sso';
    public const GARMIN_SSO_EMBED_URL = self::GARMIN_SSO_URL . '/embed';
    public const GARMIN_SSO_SIGN_IN_URL = self::GARMIN_SSO_URL . '/signin';
    public const GARMIN_SSO_MFA_URL = self::GARMIN_SSO_URL . '/verifyMFA/loginEnterMfaCode';
    public const GARMIN_AUTHENTICATION_FILE = 'garmin_credentials.json';

    public const GARMIN_DEFAULT_PARAMETERS  = [
        'id' => 'gauth-widget',
        'embedWidget' => 'true',
    ];

    public const GARMIN_EMBED_PARAMETERS = [
        ...self::GARMIN_DEFAULT_PARAMETERS,
        'gauthHost' => self::GARMIN_SSO_URL,
    ];

    public const GARMIN_SIGN_IN_PARAMETERS = [
        ...self::GARMIN_DEFAULT_PARAMETERS,
        'gauthHost' => self::GARMIN_SSO_EMBED_URL,
        'service' => self::GARMIN_SSO_EMBED_URL,
        'source' => self::GARMIN_SSO_EMBED_URL,
        'redirectAfterAccountLoginUrl' => self::GARMIN_SSO_EMBED_URL,
        'redirectAfterAccountCreationUrl' => self::GARMIN_SSO_EMBED_URL,
    ];

    protected string $consumerKey = '';
    protected string $consumerSecret = '';

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: '%env(GARMIN_USERNAME)%')]
        private string $garminUsername,
        #[Autowire(env: '%env(GARMIN_PASSWORD)%')]
        private string $garminPassword,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDirectory
    ) {
    }

    public function authenticate()
    {
        $filePath = $this->projectDirectory . DIRECTORY_SEPARATOR . self::GARMIN_AUTHENTICATION_FILE;

        // Load file from path if it exists
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $oauthData = json_decode($json, true);

            if ($oauthData['expires_at'] > time()) {
                // Great the access token isn't expired so lets reuse it from the file
                return $oauthData['access_token'];
            }
            // Access token must be expired so lets refresh it
            if ($oauthData['refresh_token_expires_at'] < time()) {
                $oauthData = $this->exchangeOauth1TokenForOauth2Token($oauthData['token'], $oauthData['token_secret']);

                $this->refreshFile($filePath, $oauthData);
                return $oauthData['access_token'];
            }
        }

        // Run through the login process which is ridiculous
        $this->fetchConsumerCredentials();
        $this->initializeCookies();
        $csrfToken = $this->fetchCSRFToken();
        $ticket = $this->submitLoginRequest($csrfToken);
        $oauth1Token = $this->getOauthToken($ticket);
        $oauthToken = $oauth1Token['oauth_token'];
        $oauthTokenSecret = $oauth1Token['oauth_token_secret'];
        $oauthData = $this->exchangeOauth1TokenForOauth2Token($oauthToken, $oauthTokenSecret);
        $oauthData['token'] = $oauthToken;
        $oauthData['token_secret'] = $oauthTokenSecret;

        $this->refreshFile($filePath, $oauthData);
        return $oauthData['access_token'];
    }

    protected function refreshFile(string $filePath, array $oauthData): void
    {
        file_put_contents($filePath, json_encode($oauthData));
    }

    protected function fetchConsumerCredentials(): void
    {
        $response = $this->httpClient->request('GET', self::GARTH_SSO_TOKENS);
        $oauth = $response->toArray();
        $this->consumerKey = $oauth['consumer_key'];
        $this->consumerSecret = $oauth['consumer_secret'];
    }

    protected function initializeCookies(): void
    {
        $this->httpClient->request('GET', self::GARMIN_SSO_EMBED_URL, [
            'query' => self::GARMIN_EMBED_PARAMETERS,
        ]);
    }

    protected function fetchCSRFToken(): string
    {
        $response = $this->httpClient->request('GET', self::GARMIN_SSO_SIGN_IN_URL, [
            'query' => self::GARMIN_SIGN_IN_PARAMETERS,
        ]);

        $responseBody = $response->getContent();

        preg_match('/name="_csrf"\s+value="(.+?)"/', $responseBody, $csrfTokens);

        if (! isset($csrfTokens[1])) {
            throw new ClientException('CSRF token is missing.');
        }

        return $csrfTokens[1];
    }

    protected function submitLoginRequest(string $csrfToken)
    {
        $response = $this->httpClient->request('POST', self::GARMIN_SSO_SIGN_IN_URL, [
            'query' => self::GARMIN_SIGN_IN_PARAMETERS,
            'headers' => [
                'referer' => self::GARMIN_SSO_SIGN_IN_URL,
            ],
            'body' => [
                'username' => $this->garminUsername,
                'password' => $this->garminPassword,
                'embed' => true,
                '_csrf' => $csrfToken,
            ]
        ]);


        $responseBody = $response->getContent(false);

        preg_match('/<title>(.+?)<\/title>/', $responseBody, $titles);

        if (! isset($titles[1])) {
            throw new ClientException('TITLE is missing.');
        }

        $title = $titles[1];

        // YA!!!!! we got into Garmin
        if ($title === 'Success') {
            preg_match('/embed\?ticket=([^"]+)"/', $responseBody, $tokens);

            if (isset($tokens[1])) {
                return $tokens[1];
            }
        }

        throw new ClientException('Invalid title!');
    }

    public function getOauthToken(string $ticket): array
    {
        $oauthRequest = new OauthHttpDecorator($this->consumerKey, $this->consumerSecret);

        $response = $oauthRequest->request(
            $this->httpClient,
            'GET',
            GarminClient::GARMIN_API_URL . '/oauth-service/oauth/preauthorized',
            [
                'query' => [
                    'ticket' => $ticket,
                    'login-url' => self::GARMIN_SSO_EMBED_URL,
                    'accepts-mfa-tokens' => 'true',
                ]
            ]
        );

        $oauth1Body = $response->getContent();

        parse_str($oauth1Body, $oauthResponseBody);

        return $oauthResponseBody;
    }

    public function exchangeOauth1TokenForOauth2Token(
        string $oauthToken,
        string $oauthTokenSecret,
    ) {
        $oauthRequest = new OauthHttpDecorator(
            $this->consumerKey,
            $this->consumerSecret,
            $oauthToken,
            $oauthTokenSecret
        );

        $oauth2Response = $oauthRequest->request(
            $this->httpClient,
            'POST',
            GarminClient::GARMIN_API_URL . '/oauth-service/oauth/exchange/user/2.0',
            [
                'headers' => [
                    'User-Agent' => GarminClient::USER_AGENT,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );

        $oauth2Data =  $oauth2Response->toArray();

        $oauth2Data['expires_at'] = $oauth2Data['expires_in'] + time();
        $oauth2Data['refresh_token_expires_at'] = $oauth2Data['refresh_token_expires_in'] + time();

        return $oauth2Data;
    }

    public function setGarminUsername(string $garminUsername): void
    {
        $this->garminUsername = $garminUsername;
    }

    public function setGarminPassword(string $garminPassword): void
    {
        $this->garminPassword = $garminPassword;
    }
}
