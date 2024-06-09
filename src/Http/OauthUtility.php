<?php

namespace App\Http;

class OauthUtility
{
    public static function generateSigningKey(string $consumerSecret, ?string $tokenSecret): string
    {
        $consumerSecret = rawurlencode($consumerSecret);
        if (isset($tokenSecret)) {
            $tokenSecret = rawurlencode($tokenSecret);
            return sprintf('%s&%s', $consumerSecret, $tokenSecret);
        }

        return sprintf('%s&', $consumerSecret);
    }

    public static function getBaseString(string $method, string $requestUri, array $queryParameters): string
    {
        $params = http_build_query($queryParameters, null, '&', PHP_QUERY_RFC3986);
        return sprintf('%s&%s&%s', $method, rawurlencode($requestUri), rawurlencode($params));
    }

    public static function getSignature(string $baseString, string $key): string
    {
        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }

    public static function getNonce(): string
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
