<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Auth;

/**
 * Tiny JWT helper: derives the token expiry from the "exp" claim, falling back
 * to a fixed lifetime when the token is not a readable JWT.
 */
final class Jwt
{
    /**
     * @param int $fallbackMinutes lifetime to assume when no exp claim is readable
     * @param int $safetySeconds   subtracted from exp so a near-expiry token is refreshed early
     */
    public static function expiry(string $jwt, int $fallbackMinutes, int $safetySeconds = 60): \DateTimeImmutable
    {
        $now = new \DateTimeImmutable();
        $parts = explode('.', $jwt);

        if (\count($parts) === 3) {
            $payload = self::decodeSegment($parts[1]);
            if (is_array($payload) && isset($payload['exp']) && is_numeric($payload['exp'])) {
                $expiresAt = $now->setTimestamp((int) $payload['exp'] - $safetySeconds);
                if ($expiresAt > $now) {
                    return $expiresAt;
                }
            }
        }

        return $now->modify(sprintf('+%d minutes', $fallbackMinutes));
    }

    /**
     * @return array<array-key, mixed>|null
     */
    private static function decodeSegment(string $segment): ?array
    {
        $b64 = strtr($segment, '-_', '+/');
        $remainder = \strlen($b64) % 4;
        if ($remainder !== 0) {
            $b64 .= str_repeat('=', 4 - $remainder);
        }

        $json = base64_decode($b64, true);
        if ($json === false) {
            return null;
        }

        $data = json_decode($json, true);

        return is_array($data) ? $data : null;
    }
}
