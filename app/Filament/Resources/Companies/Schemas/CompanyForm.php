<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('name_ar')
                    ->default(null),
                TextInput::make('slug')
                    ->required(),
                Select::make('company_type')
                    ->options([
                        'manufacturer' => 'Manufacturer',
                        'agent' => 'Agent',
                        'distributor' => 'Distributor',
                        'marketing' => 'Marketing',
                    ])
                    ->live()
                    ->required(),

                Select::make('parent_company_id')
                    ->relationship(
                        'parentCompany',
                        'name'
                    )
                    ->searchable()
                    ->preload()
                    ->visible(
                        fn ($get) => in_array(
                            $get('company_type'),
                            ['agent', 'distributor', 'marketing']
                        )
                    ),
                FileUpload::make('logo')
                    ->default(null),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('description_ar')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('country')
                    ->default(null),

                TextInput::make('governorate'),

                TextInput::make('registration_number'),

                TextInput::make('coverage_area'),

                TextInput::make('google_maps_url')
                    ->url(),

                Textarea::make('address')
                    ->columnSpanFull(),

                Textarea::make('address_ar')
                    ->columnSpanFull(),

                TextInput::make('website')
                    ->url()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('mobile')
                    ->default(null),
                TextInput::make('facebook')
                    ->default(null),
                TextInput::make('linkedin')
                    ->default(null),
                TextInput::make('contact_person')
                    ->default(null),
                TextInput::make('whatsapp')
                    ->tel()
                    ->default(null),
                TextInput::make('telegram')
                    ->default(null),
                TextInput::make('youtube')
                    ->default(null),
                TextInput::make('instagram')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
