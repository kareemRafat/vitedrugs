<?php

namespace App\Services\LargeAnimals;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use JsonException;

class DiagnosisShareService
{
    private const VERSION = 1;

    public function create(string $hostSpeciesId, array $clinicalSigns, array $refinementSigns): string
    {
        return Crypt::encryptString(json_encode([
            'version' => self::VERSION,
            'expires_at' => now()->addDay()->timestamp,
            'host_species_id' => $hostSpeciesId,
            'clinical_signs' => array_values(array_unique($clinicalSigns)),
            'refinement_signs' => array_values(array_unique($refinementSigns)),
        ], JSON_THROW_ON_ERROR));
    }

    public function read(string $token): ?array
    {
        try {
            $payload = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException|JsonException) {
            return null;
        }

        if (! is_array($payload)
            || ($payload['version'] ?? null) !== self::VERSION
            || ! is_string($payload['host_species_id'] ?? null)
            || ! is_array($payload['clinical_signs'] ?? null)
            || ! is_array($payload['refinement_signs'] ?? null)
            || ! is_int($payload['expires_at'] ?? null)
            || Carbon::createFromTimestamp($payload['expires_at'])->isPast()) {
            return null;
        }

        return $payload;
    }
}
