<?php

namespace App\Http\Controllers;

use App\Models\ActiveIngredient;
use App\Models\Blog;
use App\Models\Company;
use App\Models\Disease;
use App\Models\DosageForm;
use App\Models\Product;
use App\Models\Species;
use Illuminate\Database\Eloquent\Model;

/**
 * Serves the redesigned landing page at `/`.
 *
 * Deliberately separate from LandingController, which also serves `/drugs` and
 * must keep rendering the old design. Keeping them apart means the redesign can
 * never reach the drugs hub by accident.
 */
class HomeController extends Controller
{
    /**
     * The six entities counted on the landing page, keyed by the short handle the
     * views use. Adding one here is all that is needed for it to appear in the
     * hero strip, the stats grid and the total.
     *
     * @var array<string, class-string<Model>>
     */
    private const COUNTED = [
        'products' => Product::class,
        'diseases' => Disease::class,
        'ingredients' => ActiveIngredient::class,
        'companies' => Company::class,
        'dosageForms' => DosageForm::class,
        'species' => Species::class,
    ];

    public function __invoke()
    {
        $counts = $this->counts();
        $recent = $this->addedInLastThirtyDays();

        return view('app.landing', [
            'counts' => $counts,
            'totalRecords' => array_sum($counts),
            'recent' => $recent,
            'latestBlogs' => Blog::published()
                ->with(['category', 'author'])
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }

    /**
     * Live row counts, soft-deleted rows excluded.
     *
     * `is_active` is deliberately NOT applied. Several of these tables carry it
     * but nothing in the public site filters on it, so filtering here would make
     * the landing page disagree with the number the user sees when they clicks
     * through to the index.
     *
     * @return array<string, int>
     */
    private function counts(): array
    {
        $counts = [];

        foreach (self::COUNTED as $key => $model) {
            $counts[$key] = $model::query()->count();
        }

        return $counts;
    }

    /**
     * Rows created in the last 30 days, per entity.
     *
     * Drives the "+N this month" badges. A zero is a real answer — it means
     * nothing was added — so the views hide the badge rather than showing a
     * misleading "+0" or, worse, inventing growth.
     *
     * @return array<string, int>
     */
    private function addedInLastThirtyDays(): array
    {
        $recent = [];
        $since = now()->subDays(30);

        foreach (self::COUNTED as $key => $model) {
            $recent[$key] = $model::query()->where('created_at', '>=', $since)->count();
        }

        return $recent;
    }
}
