<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Finding;
use Illuminate\Http\Request;

class FindingSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $q = strtolower(trim($request->q));

        if (!$q) {
            return [];
        }

        $findings = Finding::query()
            ->where('canonical_name', 'like', "%{$q}%")
            ->orWhere('display_name', 'like', "%{$q}%")
            ->limit(10)
            ->get([
                'id',
                'display_name',
                'canonical_name',
                'type',
                'body_system'
            ]);

        return response()->json($findings);
    }
}