<?php

namespace App\Filament\Resources\DosageForms;

use App\Filament\Resources\DosageForms\Pages\CreateDosageForm;
use App\Filament\Resources\DosageForms\Pages\EditDosageForm;
use App\Filament\Resources\DosageForms\Pages\ListDosageForms;
use App\Filament\Resources\DosageForms\Pages\ViewDosageForm;
use App\Filament\Resources\DosageForms\Schemas\DosageFormForm;
use App\Filament\Resources\DosageForms\Schemas\DosageFormInfolist;
use App\Filament\Resources\DosageForms\Tables\DosageFormsTable;
use App\Models\DosageForm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DosageFormResource extends Resource
{
    protected static ?string $model = DosageForm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DosageFormForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DosageFormInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DosageFormsTable::configure($table);
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
            'index' => ListDosageForms::route('/'),
            'create' => CreateDosageForm::route('/create'),
            'view' => ViewDosageForm::route('/{record}'),
            'edit' => EditDosageForm::route('/{record}/edit'),
        ];
    }
}
