<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Database\Eloquent\Builder;

class SpecializationController extends Controller
{
    protected const GROUPS = [
        'infectious-diseases',
        'internal-medicine',
        'zoonotic-diseases',
    ];

    public function index()
    {
        $groups = collect(self::GROUPS)->map(fn (string $group) => [
            'key' => $group,
            'disease_count' => $this->diseasesQuery($group)->count(),
        ]);

        return view('large-animals.specializations.index', compact('groups'));
    }

    public function show(string $group)
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);

        $diseases = $this->diseasesQuery($group)->get();

        return view('large-animals.specializations.show', compact('group', 'diseases'));
    }

    private function diseasesQuery(string $group): Builder
    {
        $query = Disease::query()->where('is_active', true);

        $query = match ($group) {
            'internal-medicine' => $query->where('is_internal', true),
            'zoonotic-diseases' => $query->where('is_zoonotic', true),
            default => $query,
        };

        return $query->orderBy('name');
    }
}
