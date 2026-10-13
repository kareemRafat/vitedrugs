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

        $groups = $searched ? $this->searchService->search($part, $term) : [];

        return view('search.index', [
            'part' => $part,
            'term' => $term,
            'searched' => $searched,
            'groups' => $groups,
            'total' => $this->resultCount($groups),
        ]);
    }

    /**
     * Powers the suggestions dropdown.
     *
     * Rate limited on the route rather than here, so a 429 is produced by the
     * framework's throttle middleware and the payload stays a plain shape.
     */
    private function part(Request $request): string
    {
        return (string) ($request->attributes->get('part') ?? 'large-animals');
    }

    /**
     * @param  array<int, array{items: iterable<array<string, mixed>>}>  $groups
     */
    private function resultCount(array $groups): int
    {
        return collect($groups)->sum(fn (array $group): int => count($group['items']));
    }
}
