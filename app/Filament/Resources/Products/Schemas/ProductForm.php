<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use App\Models\Company;
use App\Models\DosageForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('manufacturer_id')
                    ->label('Manufacturer')
                    ->options(Company::pluck('name', 'id'))
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

                Section::make('Submitted')
                    ->schema([
                        Select::make('status')
                            ->options(ProductStatus::class)
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('createdBy.name')
                            ->label('Submitted By')
                            ->disabled()
                            ->dehydrated(false),
                        Textarea::make('admin_notes')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
