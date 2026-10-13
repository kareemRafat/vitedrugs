<?php

namespace App\Http\Requests\Concerns;

use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

/**
 * Keeps a submitted species or clinical sign inside the site part that posted it.
 *
 * The suggestions endpoints are already scoped, but validation runs separately
 * and a hand-crafted POST can carry any id it likes. Without these rules a
 * large-animal sign would pass a plain `Rule::exists` and reach a poultry scorer
 * that has no matching disease, producing a silently wrong result rather than an
 * error the user can act on.
 *
 * The two vocabularies differ and are mapped separately:
 *   species  `host_species.taxonomy_group`, via config('vetpedia.parts')
 *   signs    no taxonomy at all — reachable only through `diseases.domain`
 */
trait ScopesToSitePart
{
    /**
     * The themed part this request belongs to: `large-animals` or `poultry`.
     */
    abstract protected function part(): string;

    protected function domain(): string
    {
        return (string) config("vetpedia.parts.{$this->part()}.domain");
    }

    /**
     * @return array<int, string>
     */
    protected function taxonomyGroups(): array
    {
        return (array) config("vetpedia.parts.{$this->part()}.taxonomy_groups");
    }

    protected function speciesExists(): Exists
    {
        return Rule::exists(HostSpecies::class, 'id')
            ->whereIn('taxonomy_group', $this->taxonomyGroups());
    }

    /**
     * Assert the sign is linked to at least one disease in this part.
     *
     * Composed alongside a plain existence rule rather than replacing it, so a
     * missing id still reports the "does not exist" message and only a sign that
     * exists elsewhere reports the part-specific one.
     */
    protected function clinicalSignInPart(string $message): Closure
    {
        $domain = $this->domain();

        return function (string $attribute, mixed $value, Closure $fail) use ($domain, $message): void {
            $exists = ClinicalSign::query()
                ->whereKey($value)
                ->whereHas('diseases', fn (Builder $diseases): Builder => $diseases->where('domain', $domain))
                ->exists();

            if (! $exists) {
                $fail($message);
            }
        };
    }
}
