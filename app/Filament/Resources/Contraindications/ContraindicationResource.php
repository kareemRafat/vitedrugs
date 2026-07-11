<?php

namespace App\Filament\Resources\Contraindications;

use App\Filament\Resources\Contraindications\Pages\CreateContraindication;
use App\Filament\Resources\Contraindications\Pages\EditContraindication;
use App\Filament\Resources\Contraindications\Pages\ListContraindications;
use App\Filament\Resources\Contraindications\Pages\ViewContraindication;
use App\Filament\Resources\Contraindications\Schemas\ContraindicationForm;
use App\Filament\Resources\Contraindications\Schemas\ContraindicationInfolist;
use App\Filament\Resources\Contraindications\Tables\ContraindicationsTable;
use App\Models\Contraindication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContraindicationResource extends Resource
{
    protected static ?string $model = Contraindication::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return ContraindicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContraindicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContraindicationsTable::configure($table);
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
            'index' => ListContraindications::route('/'),
            'create' => CreateContraindication::route('/create'),
            'view' => ViewContraindication::route('/{record}'),
            'edit' => EditContraindication::route('/{record}/edit'),
        ];
    }
}
