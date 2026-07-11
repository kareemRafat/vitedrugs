<?php

namespace App\Filament\Resources\Precautions;

use App\Filament\Resources\Precautions\Pages\CreatePrecaution;
use App\Filament\Resources\Precautions\Pages\EditPrecaution;
use App\Filament\Resources\Precautions\Pages\ListPrecautions;
use App\Filament\Resources\Precautions\Pages\ViewPrecaution;
use App\Filament\Resources\Precautions\Schemas\PrecautionForm;
use App\Filament\Resources\Precautions\Schemas\PrecautionInfolist;
use App\Filament\Resources\Precautions\Tables\PrecautionsTable;
use App\Models\Precaution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrecautionResource extends Resource
{
    protected static ?string $model = Precaution::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return PrecautionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrecautionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrecautionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrecautions::route('/'),
            'create' => CreatePrecaution::route('/create'),
            'view' => ViewPrecaution::route('/{record}'),
            'edit' => EditPrecaution::route('/{record}/edit'),
        ];
    }
}
