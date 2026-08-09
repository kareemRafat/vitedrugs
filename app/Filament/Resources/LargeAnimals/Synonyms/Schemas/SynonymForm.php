<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms\Schemas;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\Modifier;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SynonymForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('term')
                    ->required()
                    ->maxLength(255),

                TextInput::make('normalized_term')
                    ->maxLength(255),

                TextInput::make('source_type')
                    ->maxLength(255),

                MorphToSelect::make('synonymable')
                    ->types([
                        MorphToSelect\Type::make(Disease::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(ClinicalSign::class)
                            ->titleAttribute('display_name'),
                        MorphToSelect\Type::make(Finding::class)
                            ->titleAttribute('display_name'),
                        MorphToSelect\Type::make(Modifier::class)
                            ->titleAttribute('display_name'),
                    ])
                    ->searchable()
                    ->required(),

            ]);
    }
}
