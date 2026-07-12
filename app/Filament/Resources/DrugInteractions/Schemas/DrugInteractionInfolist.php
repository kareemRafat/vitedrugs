<?php

namespace App\Filament\Resources\DrugInteractions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DrugInteractionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('activeIngredient.name'),
                TextEntry::make('interacting_drug'),
                TextEntry::make('severity')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'major' => 'danger',
                        'moderate' => 'warning',
                        'minor' => 'success',
                        default => 'gray',
                    }),
                TextEntry::make('effect'),
                TextEntry::make('recommendation'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
