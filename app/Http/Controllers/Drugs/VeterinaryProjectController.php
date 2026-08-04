<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Drugs\VeterinaryProject;

class VeterinaryProjectController extends Controller
{
    public function index()
    {
        $projects = VeterinaryProject::query()
            ->where('is_published', true)
            ->latest()
            ->paginate(9);

        return view('drugs.projects.index', compact('projects'));
    }

    public function show(string $slug)
    {
        $project = VeterinaryProject::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('drugs.projects.show', compact('project'));
    }
}
