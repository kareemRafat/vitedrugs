<?php

namespace App\Services\LargeAnimals;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use JsonException;

/**
 * Turns a set of filter criteria into a self-contained, encrypted URL token.
 *
 * The payload carries the domain for the same reason the diagnosis token does:
 * a link minted under one part must not replay under the other.
 */
class FilterShareService
{
    private const VERSION = 2;

    /**
     * @param  array<string, mixed>  $criteria
     */
    public function create(array $criteria, string $domain): string
    {
        $encrypted = Crypt::encryptString(json_encode([
            'version' => self::VERSION,
            'expires_at' => now()->addDay()->timestamp,
            'domain' => $domain,
            'host_species_id' => $criteria['host_species_id'] ?? null,
            'clinical_signs' => array_values(array_unique($criteria['clinical_signs'])),
            'etiology_type' => $criteria['etiology_type'] ?? null,
            'body_system_id' => $criteria['body_system_id'] ?? null,
            'zoonotic' => (bool) ($criteria['zoonotic'] ?? false),
        ], JSON_THROW_ON_ERROR));

        return self::base64UrlEncode($encrypted);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function read(string $token, string $domain): ?array
    {
        try {
            $payload = json_decode(Crypt::decryptString(self::base64UrlDecode($token)), true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException|JsonException|ValueError) {
            return null;
        }

        if (! is_array($payload)
            || ($payload['version'] ?? null) !== self::VERSION
            || ($payload['domain'] ?? null) !== $domain
            || ! (is_int($payload['host_species_id'] ?? null) || ($payload['host_species_id'] ?? null) === null)
            || ! is_array($payload['clinical_signs'] ?? null)
            || ! is_int($payload['expires_at'] ?? null)
            || Carbon::createFromTimestamp($payload['expires_at'])->isPast()) {
            return null;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): string
    {
        $decoded = strtr($value, '-_', '+/');
        $remainder = strlen($decoded) % 4;

        if ($remainder !== 0) {
            $decoded .= str_repeat('=', 4 - $remainder);
        }

        return (string) base64_decode($decoded, true);
    }
}
