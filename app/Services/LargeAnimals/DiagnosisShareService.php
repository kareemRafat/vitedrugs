<?php

namespace App\Services\LargeAnimals;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use JsonException;

/**
 * Turns a diagnosis assessment into a self-contained, encrypted URL token.
 *
 * Nothing is stored server-side: the payload is JSON-encoded, encrypted and
 * handed back as a query string. That makes the token cheap but also means it
 * carries everything needed to rebuild the results, so the domain has to travel
 * with it. Without it a link minted under one part would replay under the other
 * and render the wrong disease set.
 */
class DiagnosisShareService
{
    /**
     * Bumped whenever the payload shape changes. A token whose version does not
     * match is rejected rather than migrated, which invalidates links minted
     * before the change — acceptable because tokens only live for a day.
     */
    private const VERSION = 2;

    /**
     * @param  array<int, mixed>  $clinicalSigns
     * @param  array<int, mixed>  $refinementSigns
     */
    public function create(string $domain, string $hostSpeciesId, array $clinicalSigns, array $refinementSigns): string
    {
        return self::base64UrlEncode(Crypt::encryptString(json_encode([
            'version' => self::VERSION,
            'expires_at' => now()->addDay()->timestamp,
            'domain' => $domain,
            'host_species_id' => $hostSpeciesId,
            'clinical_signs' => array_values(array_unique($clinicalSigns)),
            'refinement_signs' => array_values(array_unique($refinementSigns)),
        ], JSON_THROW_ON_ERROR)));
    }

    /**
     * Read a token, rejecting it when it was minted for a different domain.
     *
     * @return array{version: int, expires_at: int, domain: string, host_species_id: string, clinical_signs: array, refinement_signs: array}|null
     */
    public function read(string $token, string $domain): ?array
    {
        try {
            $payload = json_decode(
                Crypt::decryptString(self::base64UrlDecode($token)),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (DecryptException|JsonException) {
            return null;
        }

        if (! is_array($payload)
            || ($payload['version'] ?? null) !== self::VERSION
            || ($payload['domain'] ?? null) !== $domain
            || ! is_string($payload['host_species_id'] ?? null)
            || ! is_array($payload['clinical_signs'] ?? null)
            || ! is_array($payload['refinement_signs'] ?? null)
            || ! is_int($payload['expires_at'] ?? null)
            || Carbon::createFromTimestamp($payload['expires_at'])->isPast()) {
            return null;
        }

        return $payload;
    }

    /**
     * URL-safe base64. Crypt's own output is standard base64 and can contain
     * `+`, which a query string decodes as a space and breaks the token.
     */
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
