<?php
namespace Src\Helpers;
class Jwt
{
    public static function base64url($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    public static function sign(array $payload, string $secret, string $alg = 'HS256')
    {
        $header = ['typ' => 'JWT', 'alg' => $alg];
        $segments = [
            self::base64url(json_encode($header)),
            self::base64url(json_encode($payload))
        ];
        $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
        $segments[] = self::base64url($signature);
        return implode('.', $segments);
    }
    public static function verify(string $jwt, string $secret)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }
        [$header64, $payload64, $signature64] = $parts;
        $check = self::base64url(
            hash_hmac('sha256', "$header64.$payload64", $secret, true)
        );
        if (!hash_equals($check, $signature64)) {
            return null; 
        }
        $payload = json_decode(base64_decode(strtr($payload64, '-_', '+/')), true);
        if (isset($payload['exp']) && time() > $payload['exp']) {
            return null;
        }
        return $payload;
    }
}
