<?php

namespace App\Filament\Resources\ProductSubmissions\Pages;

use App\Filament\Resources\ProductSubmissions\ProductSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListProductSubmissions extends ListRecords
{
    protected static string $resource = ProductSubmissionResource::class;
}
