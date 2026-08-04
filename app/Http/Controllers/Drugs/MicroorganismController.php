<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Drugs\Microorganism;

class MicroorganismController extends Controller
{
    public const MICROORGANISM_TYPES = [
        'bacteria',
        'virus',
        'fungus',
        'parasite',
        'other',
    ];

    public function index()
    {
        $microorganisms = Microorganism::query()
            ->orderBy('name')
            ->get();

        $grouped = $microorganisms->groupBy(fn ($microorganism) => $microorganism->microorganism_type ?? 'other');

        return view('drugs.microorganisms.index', compact('grouped'));
    }

    public function show(string $slug)
    {
        $microorganism = Microorganism::query()
            ->where('slug', $slug)
            ->with('diseases')
            ->firstOrFail();

        return view('drugs.microorganisms.show', compact('microorganism'));
    }
}
