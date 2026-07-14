<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\ProductImage;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('image')
                ->disk('public')
                ->directory(fn (): string => 'products/'.$this->getOwnerRecord()->getKey())
                ->multiple()
                ->maxFiles(3)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->visibility('public')
                    ->width(60)
                    ->height(60)
                    ->square(),

                TextColumn::make('sort_order'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data, RelationManager $livewire): ProductImage {
                        $paths = (array) ($data['image'] ?? []);
                        $relationship = $this->getRelationship();

                        $first = $relationship->create(['image' => $paths[0]]);

                        foreach (array_slice($paths, 1) as $path) {
                            $relationship->create(['image' => $path]);
                        }

                        return $first;
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->before(function (ProductImage $record): void {
                        Storage::disk('public')->delete($record->image);
                    }),
            ]);
    }
}
