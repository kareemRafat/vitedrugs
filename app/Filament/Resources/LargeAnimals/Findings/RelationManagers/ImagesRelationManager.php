<?php

namespace App\Filament\Resources\LargeAnimals\Findings\RelationManagers;

use App\Models\LargeAnimals\FindingImage;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

/**
 * Photographs for one finding.
 *
 * Modelled on `Products/RelationManagers/ImagesRelationManager`, with three
 * deliberate differences.
 *
 * One file per submit rather than `->multiple()`. Products accepts up to three
 * images because a product photograph shows the product from different angles.
 * A gross lesion is one specimen — there is no second angle worth having — and
 * the caption belongs to that one image, which a `multiple()` upload cannot
 * carry: it returns a bare array of paths with nowhere to put text. So the
 * create form takes the file and its caption together, and `sort_order` remains
 * for the cases that genuinely need more than one.
 *
 * `->image()` and `->maxSize()`, which the product version does not have. This
 * is a deviation from that precedent and it is deliberate: that upload accepts
 * whatever it is given, and a gross-lesion photograph is the largest file anyone
 * will put in this application.
 *
 * A directory per finding rather than one shared folder, for the same reason
 * `ImagesRelationManager` already does it that way — 640 findings in a single
 * directory is not browsable, and the product photographs are in a shared one
 * only because there are far fewer of them.
 */
class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('image')
                ->disk('public')
                ->directory(fn (): string => 'findings/'.$this->getOwnerRecord()->getKey())
                ->visibility('public')
                ->image()
                ->maxSize(8192)
                ->required(),

            /**
             * A gross-lesion photograph without a label is close to useless on
             * the atlas: "liver, pale and mottled" is the finding, and the picture
             * only illustrates it. Both locales, because the atlas renders in
             * Arabic and an English-only caption there reads as an unfinished page.
             */
            TextInput::make('caption')
                ->label('Caption (English)')
                ->maxLength(255),

            TextInput::make('caption_ar')
                ->label('Caption (Arabic)')
                ->maxLength(255),

            TextInput::make('sort_order')
                ->numeric()
                ->default(0),
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

                TextColumn::make('localized_caption')
                    ->label('Caption')
                    ->placeholder('—')
                    ->limit(60),

                TextColumn::make('sort_order'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make()
                    // The row goes; the file must go with it. Without this the
                    // uploads directory grows without bound and nothing in the
                    // application ever references the leftovers again.
                    ->before(function (FindingImage $record): void {
                        Storage::disk('public')->delete($record->image);
                    }),
            ]);
    }
}
