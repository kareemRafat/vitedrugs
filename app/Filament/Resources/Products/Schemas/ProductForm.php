<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\DosageForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('trade_name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('trade_name_ar'),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('dosage_form_id')
                    ->label('Dosage Form')
                    ->options(
                        DosageForm::query()
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),

                Select::make('product_type')
                    ->options([
                        'pharmaceutical' => 'Pharmaceutical',
                        'vaccine' => 'Vaccine',
                        'supplement' => 'Supplement',
                        'feed_additive' => 'Feed Additive',
                        'disinfectant' => 'Disinfectant',
                        'biological' => 'Biological',
                    ])
                    ->required()
                    ->default('pharmaceutical'),

                Textarea::make('description')
                    ->columnSpanFull(),

                Textarea::make('description_ar')
                    ->columnSpanFull(),

                TextInput::make('package_size'),

                Textarea::make('storage_conditions')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->default(true),

            ]);
    }
}
