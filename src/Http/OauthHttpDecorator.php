<?php

namespace App\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OauthHttpDecorator
{
    public function __construct(
        private string $consumerKey,
        private string $consumerSecret,
        private string $token = '',
        private string $tokenSecret = '',
    ) {
    }

    public function request(
        HttpClientInterface $client,
        string $method,
        string $requestUri,
        array $options = [],
    ) {
        if (! isset($options['query'])) {
            $options['query'] = [];
        }

        $options = $this->getQueryParameters($method, $requestUri, $options);

        return $client->request($method, $requestUri, $options);
    }

    protected function getQueryParameters(string $method, string $requestUri, array $additionalParameters): array
    {
        $queryParameters = array_merge(
            $additionalParameters['query'],
            [
                'oauth_consumer_key' => $this->consumerKey,
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_timestamp' => date('U'),
                'oauth_nonce' => OauthUtility::getNonce(),
                'oauth_version' => '1.0'
            ]
        );

        if (! empty($this->token)) {
            $queryParameters['oauth_token'] = $this->token;
        }

        ksort($queryParameters);
        $baseString = OauthUtility::getBaseString($method, $requestUri, $queryParameters);
        $signingKey = OauthUtility::generateSigningKey($this->consumerSecret, $this->tokenSecret);

        $queryParameters['oauth_signature'] = OauthUtility::getSignature($baseString, $signingKey);

        $additionalParameters['query'] = $queryParameters;

        return $additionalParameters;
    }
}
