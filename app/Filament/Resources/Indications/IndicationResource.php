<?php

namespace App\Filament\Resources\Indications;

use App\Filament\Resources\Indications\Pages\CreateIndication;
use App\Filament\Resources\Indications\Pages\EditIndication;
use App\Filament\Resources\Indications\Pages\ListIndications;
use App\Filament\Resources\Indications\Pages\ViewIndication;
use App\Filament\Resources\Indications\Schemas\IndicationForm;
use App\Filament\Resources\Indications\Schemas\IndicationInfolist;
use App\Filament\Resources\Indications\Tables\IndicationsTable;
use App\Models\Indication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IndicationResource extends Resource
{
    protected static ?string $model = Indication::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return IndicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IndicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicationsTable::configure($table);
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
            'index' => ListIndications::route('/'),
            'create' => CreateIndication::route('/create'),
            'view' => ViewIndication::route('/{record}'),
            'edit' => EditIndication::route('/{record}/edit'),
        ];
    }
}
