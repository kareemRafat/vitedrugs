<?php

namespace App\Filament\Resources\ProductSubmissions;

use App\Filament\Resources\ProductSubmissions\Pages\ListProductSubmissions;
use App\Filament\Resources\ProductSubmissions\Pages\ViewProductSubmission;
use App\Filament\Resources\ProductSubmissions\Schemas\ProductSubmissionInfolist;
use App\Filament\Resources\ProductSubmissions\Tables\ProductSubmissionsTable;
use App\Models\ProductSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductSubmissionResource extends Resource
{
    protected static ?string $model = ProductSubmission::class;

    protected static ?string $navigationLabel = 'Product Submissions';

    protected static ?string $modelLabel = 'Product Submission';

    protected static ?string $pluralModelLabel = 'Product Submissions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    public static function infolist(Schema $schema): Schema
    {
        return ProductSubmissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductSubmissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductSubmissions::route('/'),
            'view' => ViewProductSubmission::route('/{record}'),
        ];
    }
}
