<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Models\LargeAnimals\VeterinaryProject;

class VeterinaryProjectController extends Controller
{
    public function index()
    {
        $projects = VeterinaryProject::query()
            ->where('is_published', true)
            ->latest()
            ->paginate(9);

        return view('large-animals.projects.index', compact('projects'));
    }

    public function show(string $slug)
    {
        $project = VeterinaryProject::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('large-animals.projects.show', compact('project'));
    }
}
