<?php

namespace App\Filament\Resources\LargeAnimals\Findings;

use App\Filament\Resources\LargeAnimals\Findings\Pages\CreateFinding;
use App\Filament\Resources\LargeAnimals\Findings\Pages\EditFinding;
use App\Filament\Resources\LargeAnimals\Findings\Pages\ListFindings;
use App\Filament\Resources\LargeAnimals\Findings\Pages\ViewFinding;
use App\Filament\Resources\LargeAnimals\Findings\Schemas\FindingForm;
use App\Filament\Resources\LargeAnimals\Findings\Schemas\FindingInfolist;
use App\Filament\Resources\LargeAnimals\Findings\Tables\FindingsTable;
use App\Models\LargeAnimals\Finding;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FindingResource extends Resource
{
    protected static ?string $model = Finding::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return FindingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FindingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FindingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFindings::route('/'),
            'create' => CreateFinding::route('/create'),
            'view' => ViewFinding::route('/{record}'),
            'edit' => EditFinding::route('/{record}/edit'),
        ];
    }
}
