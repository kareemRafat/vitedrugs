<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaAssetResource\Pages;
use App\Filament\Resources\MediaAssetResource\Pages\CreateMediaAsset;
use App\Filament\Resources\MediaAssetResource\Pages\ListMediaAssets;
use App\Models\Disease;
use App\Models\Finding;
use App\Models\Lesion;
use App\Models\MediaAsset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\MediaAssetResource\Pages\EditMediaAsset;


class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static ?string $navigationIcon =
    'heroicon-o-photo';

    protected static ?string $navigationGroup =
    'Knowledge Base';

    protected static ?string $navigationLabel =
    'Media Library';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Grid::make(2)->schema([

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Select::make('image_type')
                    ->options([

                        'clinical' =>
                        'Clinical Image',

                        'gross_lesion' =>
                        'Gross Lesion',

                        'necropsy' =>
                        'Necropsy',

                        'histopathology' =>
                        'Histopathology',

                        'microscopy' =>
                        'Microscopy',

                        'radiology' =>
                        'Radiology',

                        'anatomy' =>
                        'Anatomical Image',

                        'clinical_case' =>
                        'Clinical Case',

                        'other' =>
                        'Other',
                    ])
                    ->required(),

            ]),

            Textarea::make('caption')
                ->rows(3),

            Grid::make(3)->schema([

                Select::make('mediable_type')
                    ->label('Entity Type')
                    ->options([

                        Disease::class =>
                        'Disease',

                        Finding::class =>
                        'Finding',

                        Lesion::class =>
                        'Lesion',
                    ])
                    ->live()
                    ->required(),

                Select::make('mediable_id')

                    ->label('Entity')

                    ->options(function (
                        callable $get
                    ) {

                        $type =
                            $get('mediable_type');

                        if (! $type) {
                            return [];
                        }

                        return $type::query()
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(function ($item) {

                                return [

                                    $item->id =>
                                    $item->name
                                        ?? $item->display_name
                                ];
                            });
                    })

                    ->searchable()

                    ->preload()

                    ->required(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

            ]),

            FileUpload::make('image_path')

                ->label('Image')

                ->directory('media')

                ->image()

                ->imageEditor()

                ->required(),

            Grid::make(2)->schema([

                Toggle::make('featured')
                    ->default(false),

                TextInput::make('source')
                    ->maxLength(255),

            ]),

            TextInput::make('copyright')
                ->maxLength(255),

        ]);
    }

    public static function table(
        Table $table
    ): Table {

        return $table

            ->columns([

                ImageColumn::make(
                    'image_path'
                )
                    ->square(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make(
                    'image_type'
                )
                    ->badge(),

                TextColumn::make(
                    'mediable_type'
                )
                    ->formatStateUsing(
                        fn($state) =>
                        class_basename($state)
                    ),

                TextColumn::make(
                    'mediable.name'
                )
                    ->label('Related Entity')
                    ->searchable(),

                TextColumn::make(
                    'sort_order'
                ),

                TextColumn::make(
                    'created_at'
                )
                    ->dateTime(
                        'Y-m-d'
                    ),

            ])

            ->filters([])

            ->actions([

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])

            ->bulkActions([

                Tables\Actions\DeleteBulkAction::make(),

            ]);
    }

    public static function getPages(): array
    {
        return [

            'index' =>
            ListMediaAssets::route('/'),

            'create' =>
            CreateMediaAsset::route('/create'),

            'edit' =>
            EditMediaAsset::route('/{record}/edit'),

        ];
    }
}
