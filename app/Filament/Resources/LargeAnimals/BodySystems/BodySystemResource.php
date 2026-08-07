<?php

namespace App\Filament\Resources\LargeAnimals\BodySystems;

use App\Filament\Resources\LargeAnimals\BodySystems\Pages\CreateBodySystem;
use App\Filament\Resources\LargeAnimals\BodySystems\Pages\EditBodySystem;
use App\Filament\Resources\LargeAnimals\BodySystems\Pages\ListBodySystems;
use App\Filament\Resources\LargeAnimals\BodySystems\Pages\ViewBodySystem;
use App\Filament\Resources\LargeAnimals\BodySystems\Schemas\BodySystemForm;
use App\Filament\Resources\LargeAnimals\BodySystems\Schemas\BodySystemInfolist;
use App\Filament\Resources\LargeAnimals\BodySystems\Tables\BodySystemsTable;
use App\Models\LargeAnimals\BodySystem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BodySystemResource extends Resource
{
    protected static ?string $model = BodySystem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return BodySystemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BodySystemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BodySystemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBodySystems::route('/'),
            'create' => CreateBodySystem::route('/create'),
            'view' => ViewBodySystem::route('/{record}'),
            'edit' => EditBodySystem::route('/{record}/edit'),
        ];
    }
}
