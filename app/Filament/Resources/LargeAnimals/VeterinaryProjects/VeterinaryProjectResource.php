<?php

namespace App\Filament\Resources\LargeAnimals\VeterinaryProjects;

use App\Filament\Resources\LargeAnimals\VeterinaryProjects\Pages\CreateVeterinaryProject;
use App\Filament\Resources\LargeAnimals\VeterinaryProjects\Pages\EditVeterinaryProject;
use App\Filament\Resources\LargeAnimals\VeterinaryProjects\Pages\ListVeterinaryProjects;
use App\Filament\Resources\LargeAnimals\VeterinaryProjects\Pages\ViewVeterinaryProject;
use App\Filament\Resources\LargeAnimals\VeterinaryProjects\Schemas\VeterinaryProjectForm;
use App\Filament\Resources\LargeAnimals\VeterinaryProjects\Schemas\VeterinaryProjectInfolist;
use App\Filament\Resources\LargeAnimals\VeterinaryProjects\Tables\VeterinaryProjectsTable;
use App\Models\LargeAnimals\VeterinaryProject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VeterinaryProjectResource extends Resource
{
    protected static ?string $model = VeterinaryProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return VeterinaryProjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VeterinaryProjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VeterinaryProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVeterinaryProjects::route('/'),
            'create' => CreateVeterinaryProject::route('/create'),
            'view' => ViewVeterinaryProject::route('/{record}'),
            'edit' => EditVeterinaryProject::route('/{record}/edit'),
        ];
    }
}
