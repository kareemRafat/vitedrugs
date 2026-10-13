<?php

namespace App\Services\Knowledge;

use App\Models\Disease;
use App\Models\LargeAnimals\Microorganism;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

/**
 * The one search behind both the large-animals and the poultry parts.
 *
 * Four things make this less obvious than a name LIKE query:
 *
 *   1. The searchable models use three different vocabularies for part
 *      membership. `Disease` has a `domain` column, `HostSpecies` has none and
 *      is classified by `taxonomy_group`, and `ClinicalSign`/`Product` have
 *      neither and are reachable only through a disease of the right domain.
 *      Each bucket declares which it uses in config/search.php.
 *
 *   2. Filtering by taxonomy alone is not enough. 14 of the 384 rows in
 *      `disease_host_species` cross the domain line, so anything that walks
 *      from a species to its diseases has to apply the disease domain as well.
 *
 *   3. Matching is LIKE first and near-miss second. The near-miss pass only
 *      runs when the LIKE pass came back short, so the common case stays a
 *      single indexed-ish query per bucket.
 *
 *   4. Bucket membership is per part. The poultry part has no blog,
 *      microorganism or medical-article content, so it simply does not list
 *      those buckets.
 */
class SearchService
{
    /**
     * The buckets a part shows, in display order.
     *
     * @return array<int, string>
     */
    public function bucketsFor(string $part): array
    {
        return (array) config("search.parts.{$part}", []);
    }

    public function serves(string $part): bool
    {
        return $this->bucketsFor($part) !== [];
    }

    /**
     * Full results page: one entry per configured bucket, empty buckets
     * included so the page can show its per-section "nothing matched" state.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $part, string $term): array
    {
        $groups = [];

        foreach ($this->bucketsFor($part) as $bucket) {
            $groups[] = $this->group($part, $bucket, $term, (int) config('search.limits.results', 20));
        }

        return $groups;
    }

    /**
     * @return array<string, mixed>
     */
    private function group(string $part, string $bucket, string $term, int $limit): array
    {
        return [
            'key' => $bucket,
            'label' => __("search.buckets.{$bucket}.label"),
            'icon' => config("search.buckets.{$bucket}.icon"),
            'items' => $this->collectFor($part, $bucket, $term, $limit),
        ];
    }

    /**
     * LIKE pass, topped up by a near-miss pass when it came up short.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function collectFor(string $part, string $bucket, string $term, int $limit): Collection
    {
        if ($limit <= 0 || trim($term) === '') {
            return collect();
        }

        $definition = $this->definition($bucket);

        if ($definition === null) {
            return collect();
        }

        /** @var array<int, string> $columns */
        $columns = (array) $definition['columns'];

        $fetch = $limit * 2;

        // Forced onto a base collection: the rows below are arrays, and merging
        // an Eloquent collection of arrays makes it try to match them by key.
        $scored = collect(
            $this->likePass($part, $bucket, $term, $columns, $fetch)
                ->map(fn (Model $model): array => [
                    'model' => $model,
                    'score' => $this->matchScore($model, $columns, $term),
                ])
                ->all()
        );

        if ($scored->count() < $limit) {
            // Keys, not models: whereNotIn against a column needs scalars, and
            // passing models silently excluded nothing — which let the near-miss
            // pass return every row the LIKE pass had already found.
            $alreadyFound = $scored->pluck('model')->map(fn (Model $model): mixed => $model->getKey())->all();

            $scored = $scored
                ->merge($this->nearMissPass($part, $bucket, $term, $columns, $alreadyFound)->all())
                ->sortByDesc('score')
                ->values();
        }

        return $scored
            ->take($limit)
            ->map(fn (array $row): array => $this->present($part, $bucket, $definition, $row['model']))
            ->values();
    }

    /**
     * The fast path. `LIKE '%term%'` across every searchable column.
     *
     * Over-fetched by the caller so the ranking step has something to reorder —
     * the database returns rows in index order, which is not relevance order.
     *
     * @param  array<int, string>  $columns
     * @return Collection<int, Model>
     */
    private function likePass(string $part, string $bucket, string $term, array $columns, int $limit): Collection
    {
        $pattern = '%'.$this->escapeLike($term).'%';

        return $this->queryFor($part, $bucket)
            ->where(function (Builder $query) use ($columns, $pattern): void {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', $pattern);
                }
            })
            ->limit($limit)
            ->get();
    }

    /**
     * The slow path, reached only when LIKE found too little.
     *
     * Scored in PHP because neither `clinical_signs` nor `host_species` has a
     * fulltext index — only `diseases` and `products` do, and only on MySQL.
     *
     * @param  array<int, string>  $columns
     * @param  array<int, mixed>  $exclude  keys the LIKE pass already found
     * @return Collection<int, array{model: Model, score: int}>
     */
    private function nearMissPass(string $part, string $bucket, string $term, array $columns, array $exclude): Collection
    {
        if (mb_strlen($term) < (int) config('search.fuzzy.min_length', 4)) {
            return collect();
        }

        $minimum = (float) config('search.fuzzy.min_score', 0.3);
        $keyName = $this->keyNameFor($bucket);

        return $this->queryFor($part, $bucket)
            ->when($exclude !== [], fn (Builder $query): Builder => $query->whereNotIn($keyName, $exclude))
            ->limit((int) config('search.fuzzy.scan_limit', 2000))
            ->get()
            ->map(function (Model $model) use ($columns, $term, $minimum): ?array {
                $score = $this->nearMissScore($model, $columns, $term);

                return $score >= $minimum
                    ? ['model' => $model, 'score' => 300 + (int) round($score * 100)]
                    : null;
            })
            ->filter()
            ->values();
    }

    /**
     * The part-scoped base query for a bucket.
     *
     * @return Builder<Model>
     */
    private function queryFor(string $part, string $bucket): Builder
    {
        $definition = $this->definition($bucket);
        $model = $this->modelFor($bucket);

        /** @var Builder<Model> $query */
        $query = $model::query();

        $domain = (string) config("vetpedia.parts.{$part}.domain");

        match ($definition['scope'] ?? 'none') {
            'domain' => $query->forDomain($domain),
            'part' => $query->forPart($part),
            'through_disease' => $query->whereHas('diseases', fn (Builder $diseases): Builder => $diseases
                ->where('domain', $domain)),
            default => null,
        };

        // An inactive disease should not be findable.
        if ($model === Disease::class) {
            $query->where('is_active', true);
        }

        if (($definition['extra_scope'] ?? null) === 'catalogue' && $model === Microorganism::class) {
            $query->catalogue();
        }

        return $query;
    }

    /**
     * Ranking tiers, highest first. Exact beats prefix beats contains, so a
     * row literally named after the term outranks one that merely mentions it.
     *
     * @param  array<int, string>  $columns
     */
    private function matchScore(Model $model, array $columns, string $term): int
    {
        $needle = mb_strtolower(trim($term));

        $best = 0;

        foreach ($columns as $column) {
            $value = $model->getAttribute($column);

            if (! is_string($value) || $value === '') {
                continue;
            }

            $haystack = mb_strtolower($value);

            $best = max($best, match (true) {
                $haystack === $needle => 1000,
                str_starts_with($haystack, $needle) => 800,
                str_contains($haystack, $needle) => 600,
                default => 0,
            });
        }

        return $best;
    }

    /**
     * The best near-miss between the term and any word or whole value in the
     * row's searchable columns.
     *
     * A candidate only counts if it clears one of the two gates described in
     * config/search.php: the general threshold, or the lower anchored threshold.
     *
     * @param  array<int, string>  $columns
     */
    private function nearMissScore(Model $model, array $columns, string $term): float
    {
        $best = 0.0;

        foreach ($columns as $column) {
            $value = $model->getAttribute($column);

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            foreach ($this->candidates($value) as $candidate) {
                $match = $this->nearMiss($term, $candidate);

                if ($match !== null && $this->clearsThreshold($match)) {
                    $best = max($best, $match['score']);
                }
            }
        }

        return $best;
    }

    /**
     * The whole value plus each of its words, so a term can match either.
     *
     * @return array<int, string>
     */
    private function candidates(string $value): array
    {
        return array_merge([$value], preg_split('/[\s\/\-,()]+/u', $value) ?: []);
    }

    /**
     * Sørensen–Dice over character trigrams, plus whether the match is anchored
     * at the front of the candidate.
     *
     * Chosen over edit distance because the common typo is a doubled-letter
     * deletion: "cocido" -> "coccidiosis" is 5 edits away, so no edit-distance
     * threshold tight enough to be useful would catch it, while their trigrams
     * overlap directly.
     *
     * @return array{score: float, anchored: bool}|null
     */
    private function nearMiss(string $needle, string $haystack): ?array
    {
        $needleGrams = $this->trigrams($needle);
        $haystackGrams = $this->trigrams($haystack);

        if ($needleGrams === [] || $haystackGrams === []) {
            return null;
        }

        $shared = count(array_intersect($needleGrams, $haystackGrams));

        if ($shared < (int) config('search.fuzzy.min_shared', 2)) {
            return null;
        }

        return [
            'score' => (2 * $shared) / (count($needleGrams) + count($haystackGrams)),
            'anchored' => $this->isAnchored($needle, $haystack, $needleGrams, $haystackGrams),
        ];
    }

    /**
     * Whether the match starts at the front of the candidate rather than partway in.
     *
     * Two ways to qualify:
     *
     *   - the candidate opens with the query's first trigram, which covers
     *     ordinary typos;
     *   - the candidate opens with the query's first two characters, which adds
     *     transpositions. A transposition rewrites the first trigram — "pnuemonia"
     *     opens "pnu" where "pneumonia" opens "pne" — so the trigram test alone
     *     would miss it, while the leading two characters ("pn") survive.
     *
     * @param  array<int, string>  $needleGrams
     * @param  array<int, string>  $haystackGrams
     */
    private function isAnchored(string $needle, string $haystack, array $needleGrams, array $haystackGrams): bool
    {
        if (in_array($needleGrams[0], $haystackGrams, true)) {
            return true;
        }

        $leading = mb_strtolower(mb_substr(trim($needle), 0, 2));

        return $leading !== '' && str_starts_with(mb_strtolower(trim($haystack)), $leading);
    }

    /**
     * @param  array{score: float, anchored: bool}  $match
     */
    private function clearsThreshold(array $match): bool
    {
        return $match['anchored']
            && $match['score'] >= (float) config('search.fuzzy.min_score', 0.3);
    }

    /**
     * @return array<int, string>
     */
    private function trigrams(string $value): array
    {
        $value = mb_strtolower(trim($value));

        if ($value === '') {
            return [];
        }

        $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($characters) < 3) {
            return [$value];
        }

        $grams = [];

        for ($i = 0, $last = count($characters) - 3; $i <= $last; $i++) {
            $grams[] = $characters[$i].$characters[$i + 1].$characters[$i + 2];
        }

        return array_values(array_unique($grams));
    }

    /**
     * Shape one model into the array the views and the dropdown consume.
     *
     * On the Arabic locale the Arabic name becomes the heading and the English
     * one drops to the sub-line, matching the `localized_name` /
     * `localized_display_name` accessors the models already expose. Buckets
     * without an Arabic column, such as host species' taxonomy group, are
     * unaffected.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string, mixed>
     */
    private function present(string $part, string $bucket, array $definition, Model $model): array
    {
        $title = (string) $model->getAttribute($definition['title']);

        $titleAr = ($definition['title_ar'] ?? null) !== null
            ? $model->getAttribute($definition['title_ar'])
            : null;

        $subtitle = ($definition['secondary'] ?? null) !== null
            ? $model->getAttribute($definition['secondary'])
            : null;

        if (app()->getLocale() === 'ar' && is_string($titleAr) && trim($titleAr) !== '') {
            $subtitle = $title;
            $title = $titleAr;
        }

        return [
            'id' => $model->getKey(),
            'title' => $title,
            'secondary' => is_string($subtitle) && $subtitle !== '' ? $subtitle : null,
            'url' => $this->urlFor($part, $definition, $model),
        ];
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function urlFor(string $part, array $definition, Model $model): ?string
    {
        $route = str_replace('{part}', $part, (string) $definition['route']);

        if (! Route::has($route)) {
            return null;
        }

        $parameters = [];

        if (($definition['route_key'] ?? null) !== null) {
            $parameters[(string) $definition['route_key']] = $model->getAttribute($definition['route_key']);
        }

        foreach ((array) ($definition['route_params'] ?? []) as $name => $attribute) {
            $parameters[(string) $name] = $model->getAttribute((string) $attribute);
        }

        return route($route, $parameters);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function definition(string $bucket): ?array
    {
        $definition = config("search.buckets.{$bucket}");

        return is_array($definition) ? $definition : null;
    }

    /**
     * @return class-string<Model>
     */
    private function modelFor(string $bucket): string
    {
        return (string) config("search.buckets.{$bucket}.model");
    }

    /**
     * The primary key column, read from the model rather than hardcoded — the
     * buckets span ULID strings (`diseases`) and auto-increment bigints
     * (`clinical_signs`, `host_species`).
     */
    private function keyNameFor(string $bucket): string
    {
        $class = $this->modelFor($bucket);

        return (new $class)->getKeyName();
    }

    /**
     * LIKE treats % and _ as wildcards, so a term containing them has to be
     * escaped or it silently turns into a match-everything pattern.
     */
    private function escapeLike(string $term): string
    {
        return addcslashes(trim($term), '%_\\');
    }
}
