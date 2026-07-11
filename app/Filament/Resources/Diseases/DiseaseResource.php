<?php

namespace App\Filament\Resources\Diseases;

use App\Filament\Resources\Diseases\Pages\CreateDisease;
use App\Filament\Resources\Diseases\Pages\EditDisease;
use App\Filament\Resources\Diseases\Pages\ListDiseases;
use App\Filament\Resources\Diseases\Pages\ViewDisease;
use App\Filament\Resources\Diseases\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\Diseases\Schemas\DiseaseForm;
use App\Filament\Resources\Diseases\Schemas\DiseaseInfolist;
use App\Filament\Resources\Diseases\Tables\DiseasesTable;
use App\Models\Disease;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DiseaseResource extends Resource
{
    protected static ?string $model = Disease::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DiseaseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DiseaseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiseasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiseases::route('/'),
            'create' => CreateDisease::route('/create'),
            'view' => ViewDisease::route('/{record}'),
            'edit' => EditDisease::route('/{record}/edit'),
        ];
    }
}
