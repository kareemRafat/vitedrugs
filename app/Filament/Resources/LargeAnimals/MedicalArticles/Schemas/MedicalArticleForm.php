<?php

namespace App\Filament\Resources\LargeAnimals\MedicalArticles\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MedicalArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('title_ar'),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('summary')
                    ->columnSpanFull(),

                Textarea::make('summary_ar')
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->columnSpanFull()
                    ->hint('Write each section title on its own line — titles are used for the "On this page" sidebar.')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('medical-articles'),

                RichEditor::make('content_ar')
                    ->columnSpanFull()
                    ->hint('اكتب عنوان كل قسم في سطر منفصل — تُستخدم العناوين في قائمة "في هذه الصفحة".')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('medical-articles'),

                Select::make('disease_id')
                    ->label('Disease')
                    ->relationship('disease', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('species'),

                TextInput::make('species_ar'),

                Select::make('article_type')
                    ->options([
                        'disease_reference' => 'Disease Reference',
                        'review' => 'Review',
                        'research' => 'Research',
                        'case_study' => 'Case Study',
                        'guideline' => 'Guideline',
                        'overview' => 'Overview',
                    ]),

                Toggle::make('is_published')
                    ->default(true),

            ]);
    }
}
