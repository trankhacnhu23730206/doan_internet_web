<?php
namespace auth;

class JWT {
    public static function encode($payload, $secret) {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $segments = [
            self::base64Url(json_encode($header)),
            self::base64Url(json_encode($payload))
        ];
        $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
        $segments[] = self::base64Url($signature);
        return implode('.', $segments);
    }

    public static function decode($token, $secret) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$header, $payload, $signature] = $parts;

        $valid = self::base64Url(hash_hmac('sha256', "$header.$payload", $secret, true));
        if ($valid !== $signature) return null;

        return json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
    }

    private static function base64Url($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
