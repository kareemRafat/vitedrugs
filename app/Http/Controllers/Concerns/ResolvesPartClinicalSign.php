<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Resolves the clinical sign a sign page is about, scoped to one site part.
 *
 * `clinical_signs` has no domain column. A sign belongs to a part when at least
 * one disease in that part carries the sign, so the scoping is a `whereHas`
 * through `diseases` rather than a column comparison — see §9.22 in the poultry
 * plan.
 *
 * Shared rather than repeated because the scoping rule is the whole difficulty
 * here. Two thin controllers copying this query would drift the moment the rule
 * changed, and the failure mode is silent: a sign reachable from both domains
 * would start leaking across the section boundary that the rest of the page is
 * built to hold.
 *
 * Signs are addressed by `canonical_name`, not by id or slug. The table has no
 * slug column, and the canonical name is the stable, already-normalised key —
 * it is what the seeder merges duplicates on.
 *
 * **A canonical name is not unique.** `ClinicalSign` is a Finding plus an
 * anatomical structure plus a modifier, and the same concept is sometimes
 * recorded twice — a curated modifier-bearing row beside a modifier-less one.
 * Five names do this on the poultry side today (anemia, dehydration, depression,
 * diarrhea, weight loss). So a name resolves to a *set* of records, not to one,
 * and the page presents the concept rather than whichever row the database
 * happened to return first. Resolving with `first()` silently dropped half the
 * diseases on those five pages.
 */
trait ResolvesPartClinicalSign
{
    /**
     * Every record of this sign that the part can reach, or a 404.
     *
     * A name belonging only to the other part is a 404 here rather than a page:
     * the same decision the disease pages make, and for the same reason — a page
     * that renders outside its own section re-themes itself as the wrong part and
     * leaves the reader with no way back.
     *
     * Ordered by id so the record the page leads with is the same on every
     * request rather than a function of query plan.
     *
     * The diseases are not eager-loaded. They are loaded by `diseasesFor()`, which
     * has to scope them, and a closure passed to `with()` receives the *relation*
     * rather than the query builder, so constraining them there reads as a type
     * error rather than as a filter.
     *
     * @return Collection<int, ClinicalSign>
     */
    protected function clinicalSignsOrFail(string $part, string $canonicalName): Collection
    {
        $domain = $this->partDomain($part);

        $signs = ClinicalSign::query()
            ->with(['anatomicalStructure.bodySystem', 'finding', 'modifier'])
            ->where('canonical_name', $canonicalName)
            ->whereHas('diseases', fn (Builder $diseases): Builder => $diseases->where('domain', $domain))
            ->orderBy('id')
            ->get();

        if ($signs->isEmpty()) {
            abort(404);
        }

        return $signs;
    }

    /**
     * The sign's diseases across every record of it, strongest first.
     *
     * A union, and one row per disease: the same disease can be linked to both
     * records with different weights and flags.
     *
     * The heaviest association wins the weight and the three flags are OR-ed
     * across the records. That is deliberate rather than a shortcut — a marker
     * recorded against any one of the concept's records is true of the concept,
     * and dropping it because the other record did not carry it would understate
     * the sign. A tie on weight cannot change the outcome, since the flags shown
     * are the same either way.
     *
     * @param  Collection<int, ClinicalSign>  $signs
     * @return Collection<int, Disease>
     */
    protected function diseasesFor(Collection $signs, string $part): Collection
    {
        $domain = $this->partDomain($part);

        return $signs
            ->flatMap(fn (ClinicalSign $sign): Collection => $sign->diseases()
                ->forDomain($domain)
                ->where('is_active', true)
                ->get())
            ->groupBy('id')
            ->map(function (Collection $rows): Disease {
                /** @var Disease $disease */
                $disease = $rows->sortByDesc(fn (Disease $row): int => (int) ($row->pivot->weight ?? 0))->first();

                foreach (['is_pathognomonic', 'is_required', 'is_specific'] as $flag) {
                    $disease->pivot->{$flag} = $rows->contains(fn (Disease $row): bool => (bool) $row->pivot->{$flag});
                }

                $disease->pivot->weight = $rows->max(fn (Disease $row): int => (int) ($row->pivot->weight ?? 0));

                return $disease;
            })
            ->sortBy([
                fn (Disease $a, Disease $b): int => ($b->pivot->weight ?? 0) <=> ($a->pivot->weight ?? 0),
                fn (Disease $a, Disease $b): int => strcasecmp($a->name, $b->name),
            ])
            ->values();
    }

    /**
     * The detail rows that differ between the records of one sign.
     *
     * Empty when the records agree, which is the usual case — the page then shows
     * one block and says nothing about records at all.
     *
     * @param  Collection<int, ClinicalSign>  $signs
     * @return array<int, array<string, string>>
     */
    protected function recordVariants(Collection $signs): array
    {
        if ($signs->count() < 2) {
            return [];
        }

        $described = $signs->map(fn (ClinicalSign $sign): array => [
            'anatomical_structure' => (string) ($sign->anatomicalStructure?->localized_display_name ?? ''),
            'body_system' => (string) ($sign->anatomicalStructure?->bodySystem?->localized_display_name ?? ''),
            'stage' => (string) ($sign->stage ?? ''),
            'modifier' => (string) ($sign->modifier?->display_name ?? ''),
            'finding' => (string) ($sign->finding?->display_name ?? ''),
        ]);

        return $described
            ->reject(fn (array $row): bool => $row === $described->first())
            ->unique(fn (array $row): string => implode('|', $row))
            ->values()
            ->all();
    }

    protected function partDomain(string $part): string
    {
        return (string) config("vetpedia.parts.{$part}.domain");
    }
}
