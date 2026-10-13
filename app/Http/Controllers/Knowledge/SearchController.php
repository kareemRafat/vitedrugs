<?php

namespace App\Http\Controllers\Knowledge;

use App\Http\Controllers\Controller;
use App\Services\Knowledge\SearchService;
use Illuminate\Http\Request;

/**
 * Global search for both the large-animals and the poultry parts.
 *
 * One controller rather than two. The parts differ only in which buckets they
 * show and which domain those buckets are restricted to, and both of those are
 * data in config/search.php — so forking the controller would duplicate ~80
 * lines that exist only to read configuration.
 *
 * The part comes from the request, which `SetPartTheme` has already resolved
 * from the URL's first segment and shared into every view.
 */
class SearchController extends Controller
{
    public function __construct(
        private SearchService $searchService,
    ) {}

    public function index(Request $request)
    {
        $part = $this->part($request);
        $term = trim((string) $request->query('q', ''));
        $searched = $term !== '';

        $groups = $searched ? $this->searchService->search($part, $term, $this->pages($request, $part)) : [];

        return view('search.index', [
            'part' => $part,
            'term' => $term,
            'searched' => $searched,
            'groups' => $groups,
            'total' => $this->resultCount($groups),
        ]);
    }

    private function part(Request $request): string
    {
        return (string) ($request->attributes->get('part') ?? 'large-animals');
    }

    /**
     * The requested page for each bucket, read from `{bucket}_page`.
     *
     * Every bucket is asked for, not just the ones rendered, because the service
     * needs a page number to slice each collection it returns — a bucket left at
     * page 1 while the user pages its neighbours is the one visible bug.
     *
     * @return array<string, mixed>
     */
    private function pages(Request $request, string $part): array
    {
        $pages = [];

        foreach ($this->searchService->bucketsFor($part) as $bucket) {
            $pages[$bucket] = $request->query("{$bucket}_page");
        }

        return $pages;
    }

    /**
     * The grand total across every bucket.
     *
     * Read from each bucket's own total rather than by counting what it returned:
     * a bucket that matched 60 rows and is showing 10 of them has a total of 60,
     * and counting the page would report 10.
     *
     * @param  array<int, array<string, mixed>>  $groups
     */
    private function resultCount(array $groups): int
    {
        return collect($groups)->sum(fn (array $group): int => (int) $group['total']);
    }
}
