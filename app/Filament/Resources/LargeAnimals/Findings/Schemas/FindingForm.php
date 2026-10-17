<?php

namespace App\Filament\Resources\LargeAnimals\Findings\Schemas;

use App\Enums\OntologyType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FindingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Details')
                    ->schema([
                        TextInput::make('canonical_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('display_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('display_name_ar')
                            ->maxLength(255),

                        TextInput::make('category')
                            ->maxLength(255),

                        // A dropdown, not a text box. This is the one field that decides
                        // whether a row appears on the lesion atlas at all, and the atlas
                        // filter is an exact match — typing `Lesion` with a capital L used
                        // to save cleanly and leave the finding invisible on the page.
                        Select::make('ontology_type')
                            ->options(OntologyType::class)
                            ->required(),

                        Select::make('parent_id')
                            ->relationship('parent', 'display_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Toggle::make('is_noisy')
                            ->default(false),

                        Toggle::make('is_general_sign')
                            ->default(false),

                        TextInput::make('slug')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                /**
                 * A lesion arrives with its photograph, rather than needing a second
                 * trip through the Images tab to attach one. Creating a finding and
                 * photographing it were two separate tasks; they are one act.
                 *
                 * Only on create. On edit this field would have to *replace* an
                 * existing photograph rather than add one, which is a second code
                 * path racing the tab's own reorder and delete. The tab stays the
                 * single management surface — see `CreateFinding`.
                 *
                 * The three names here are deliberately **not** columns on
                 * `findings`. They are pulled out of the form state before
                 * `Finding::create()` and turned into a `finding_images` row; left in
                 * place they would be mass-assigned into a table that does not have
                 * them and throw.
                 */
                Section::make('Photograph')
                    ->description('Optional. Can also be added later from the Images tab.')
                    /**
                     * Create page only. `FindingForm` is shared between Create and
                     * Edit, and on edit this field would have to *replace* an
                     * existing photograph rather than add one — a second code path
                     * racing the Images tab's own reorder and delete. The tab stays
                     * the single management surface; see `CreateFinding`.
                     *
                     * Keyed off the page type rather than a flag, so adding a
                     * third page that uses this form later cannot accidentally
                     * inherit a photo field that its own lifecycle cannot support.
                     */
                    ->visible(fn (): bool => $schema->getLivewire() instanceof CreateRecord)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Photograph')
                            ->disk('public')
                            ->visibility('public')
                            /**
                             * Staged, not filed. Filament uploads the file when the
                             * form is submitted, which is *before* the finding row
                             * exists — `findings.id` is an auto-increment, so
                             * `findings/{id}/` cannot be named yet. `CreateFinding`
                             * moves the file once the id exists.
                             *
                             * This is the one deviation from the Images tab, which
                             * writes straight to `findings/{id}/` because there the
                             * record is already there to ask.
                             */
                            ->directory('findings/tmp')
                            /**
                             * Same validation as `ImagesRelationManager`, so the two
                             * entry points cannot drift into accepting different
                             * things. A gross-lesion photograph is the largest file
                             * anyone will put in this application.
                             *
                             * `->image()` rather than an explicit list of types: it
                             * resolves to `image/*`, and naming the types by hand
                             * overrides that with a shorter list — it drops heic and
                             * heif, which is exactly what a vet photographing on a
                             * phone will have.
                             */
                            ->image()
                            ->maxSize(8192),

                        TextInput::make('caption')
                            ->label('Caption (English)')
                            ->maxLength(255),

                        TextInput::make('caption_ar')
                            ->label('Caption (Arabic)')
                            ->maxLength(255),
                    ])
                    ->columns(1),
            ]);
    }
}
