<?php

namespace App\Services\LargeAnimals;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use JsonException;

class FilterShareService
{
    private const VERSION = 1;

    public function create(array $criteria): string
    {
        $encrypted = Crypt::encryptString(json_encode([
            'version' => self::VERSION,
            'expires_at' => now()->addDay()->timestamp,
            'host_species_id' => $criteria['host_species_id'],
            'clinical_signs' => array_values(array_unique($criteria['clinical_signs'])),
            'etiology_type' => $criteria['etiology_type'] ?? null,
            'body_system_id' => $criteria['body_system_id'] ?? null,
            'zoonotic' => (bool) ($criteria['zoonotic'] ?? false),
        ], JSON_THROW_ON_ERROR));

        return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
    }

    public function read(string $token): ?array
    {
        try {
            $padded = str_pad(strtr($token, '-_', '+/'), strlen($token) % 4 ? 4 - (strlen($token) % 4) : 0, '=');
            $payload = json_decode(Crypt::decryptString(base64_decode($padded)), true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException|JsonException|ValueError) {
            return null;
        }

        if (! is_array($payload)
            || ($payload['version'] ?? null) !== self::VERSION
            || ! is_int($payload['host_species_id'] ?? null)
            || ! is_array($payload['clinical_signs'] ?? null)
            || ! is_int($payload['expires_at'] ?? null)
            || Carbon::createFromTimestamp($payload['expires_at'])->isPast()) {
            return null;
        }

        return $payload;
    }
}
