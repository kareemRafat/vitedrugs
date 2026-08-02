<?php

namespace App\Http\Controllers\Api;

use App\Models\Microorganism;
use App\Http\Controllers\Controller;
use App\Http\Resources\MicroorganismResource;
use App\Http\Resources\MicroorganismListResource;

class MicroorganismController extends Controller
{
    public function index()
    {
        return MicroorganismListResource::collection(

            Microorganism::query()

                ->orderBy('name')

                ->paginate(20)
        );
    }

    public function show(string $slug)
    {
        $microorganism = Microorganism::query()

            ->where(
                'slug',
                $slug
            )

            ->firstOrFail();

        return MicroorganismResource::make(
            $microorganism
        );
    }
}