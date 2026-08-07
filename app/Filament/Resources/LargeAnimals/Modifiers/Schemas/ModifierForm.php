<?php

namespace App\Filament\Resources\LargeAnimals\Modifiers\Schemas;

use App\Models\LargeAnimals\Modifier;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ModifierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                KeyValue::make('display_name')
                    ->label('Facets')
                    ->keyLabel('Attribute (key)')
                    ->valueLabel('Value')
                    ->addActionLabel('Add attribute')
                    ->reorderable()
                    ->required()
                    ->live()
                    ->helperText('Add one or more attribute/value pairs, e.g. <code>color → straw-colored</code>. Canonical name, type, modifier group and the display label are generated automatically.')
                    ->formatStateUsing(fn (string|array|null $state): array => is_array($state) ? $state : self::decode($state))
                    ->dehydrateStateUsing(fn (array $state): string => json_encode(array_filter($state, fn ($value) => $value !== null && $value !== ''), JSON_UNESCAPED_UNICODE))
                    ->afterStateUpdated(fn (Set $set, ?array $state): array => self::syncGenerated($set, $state)),

                TextInput::make('display_label_preview')
                    ->label('Display label (as shown to admins)')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Preview appears here')
                    ->helperText('Read-only preview of how this modifier will be rendered in dropdowns and tables.'),

                TextInput::make('canonical_name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Auto-generated from the facets. Must stay unique across all modifiers.'),

                TextInput::make('display_name_ar')
                    ->maxLength(255)
                    ->helperText('Arabic label shown to Arabic users, e.g. ثنائي الجانب.'),

                TextInput::make('type')
                    ->maxLength(255)
                    ->helperText('Auto-filled with the facet dimension(s), e.g. pain, severity. Can be overridden.'),

                TextInput::make('modifier_group')
                    ->maxLength(255)
                    ->helperText('Auto-filled with the same dimension(s) as type. Can be overridden.'),

                Toggle::make('is_noisy')
                    ->default(false)
                    ->helperText('Enable for rare/noisy modifiers that should be deprioritized in diagnosis.'),

            ]);
    }

    private static function decode(?string $state): array
    {
        if (! is_string($state) || ! str_starts_with($state, '{')) {
            return [];
        }

        $data = json_decode($state, true);

        return is_array($data) ? $data : [];
    }

    private static function syncGenerated(Set $set, ?array $state): array
    {
        $data = array_filter($state ?? [], fn ($value) => $value !== null && $value !== '');

        if ($data === []) {
            $set('display_label_preview', null);
            $set('type', null);
            $set('modifier_group', null);

            return [];
        }

        $set('display_label_preview', Modifier::formatData($data));
        $set('canonical_name', Modifier::canonicalForData($data));

        $groups = Modifier::groupsForData($data);
        $set('type', $groups);
        $set('modifier_group', $groups);

        return $data;
    }
}
