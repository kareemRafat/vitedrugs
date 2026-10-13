<?php

namespace App\Services\Knowledge;

use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Database\Eloquent\Builder;

/**
 * Resolves the deep-link parameters that search hands to the diagnosis and
 * filter tools.
 *
 * Both tools take prefill through their landing pages rather than through a
 * separate endpoint, so a search result for a clinical sign or a host species
 * has to land somewhere meaningful. Without this the links resolved to a bare
 * tool with nothing selected, which is the same page a user would have reached
 * by giving up.
 *
 * Both resolvers return null rather than throwing. The parameters come from a
 * URL, so an id that was deleted, mistyped or belongs to the other part has to
 * degrade to an ordinary empty tool instead of a 404 — a stale bookmark or a
 * hand-edited query string should never be an error page.
 */
class ToolPrefillService
{
    /**
     * A clinical sign that the given part's diagnosis tool can actually use.
     *
     * `clinical_signs` has no domain column, so part membership is derived: a
     * sign belongs to a part when at least one disease in that part's domain
     * carries it. This is also why a sign reachable from both domains resolves
     * on either tool — the check is "reachable from here", not "exclusive to
     * here".
     */
    public function sign(string $part, mixed $id): ?ClinicalSign
    {
        $domain = $this->domain($part);
        $id = $this->usableId($id);

        if ($domain === null || $id === false) {
            return null;
        }

        return ClinicalSign::query()
            ->whereKey($id)
            ->whereHas('diseases', fn (Builder $diseases): Builder => $diseases->where('domain', $domain))
            ->first();
    }

    /**
     * A host species the given part's filter tool offers.
     */
    public function species(string $part, mixed $id): ?HostSpecies
    {
        $id = $this->usableId($id);

        if ($id === false) {
            return null;
        }

        return HostSpecies::query()->forPart($part)->find($id);
    }

    private function domain(string $part): ?string
    {
        $domain = config("vetpedia.parts.{$part}.domain");

        return is_string($domain) && $domain !== '' ? $domain : null;
    }

    /**
     * Ids arrive as query-string strings, so they are validated as integers
     * before reaching `whereKey` rather than being cast and coerced. `1.5` and
     * `1abc` are both rejected instead of quietly resolving to id 1.
     */
    private function usableId(mixed $id): int|false
    {
        return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    }
}
