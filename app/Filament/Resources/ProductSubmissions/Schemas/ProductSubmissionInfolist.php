<?php

namespace App\Filament\Resources\ProductSubmissions\Schemas;

use App\Enums\SubmissionStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductSubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Submitter Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('submitted_by_name')
                            ->label('Name'),
                        TextEntry::make('submitted_by_email')
                            ->label('Email'),
                        TextEntry::make('submitted_by_phone')
                            ->label('Phone')
                            ->placeholder('Not provided'),
                        TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->dateTime(),
                    ]),

                Section::make('Status')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (SubmissionStatus $state): string => $state->color()),
                        TextEntry::make('admin_notes')
                            ->label('Admin Notes')
                            ->placeholder('No notes')
                            ->visible(fn ($record): bool => filled($record?->admin_notes)),
                    ]),

                Section::make('Submitted Data')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('submitted_data.trade_name')
                            ->label('Trade Name'),
                        TextEntry::make('submitted_data.trade_name_ar')
                            ->label('Trade Name (Arabic)')
                            ->placeholder('Not provided'),
                        TextEntry::make('submitted_data.product_type')
                            ->label('Product Type')
                            ->badge(),
                        TextEntry::make('submitted_data.company')
                            ->label('Company'),
                        TextEntry::make('submitted_data.dosage_form')
                            ->label('Dosage Form'),
                        TextEntry::make('submitted_data.package_size')
                            ->label('Package Size')
                            ->placeholder('Not provided'),
                        TextEntry::make('submitted_data.storage_conditions')
                            ->label('Storage Conditions')
                            ->placeholder('Not provided'),
                        TextEntry::make('submitted_data.description')
                            ->label('Description')
                            ->placeholder('Not provided'),
                        TextEntry::make('submitted_data.active_ingredients')
                            ->label('Active Ingredients')
                            ->badge()
                            ->state(fn ($record): ?string => self::formatList($record, 'active_ingredients', 'name'))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.diseases')
                            ->label('Diseases')
                            ->badge()
                            ->state(fn ($record): ?string => self::formatSimpleList($record, 'diseases'))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.indications')
                            ->label('Indications')
                            ->state(fn ($record): ?string => self::formatSimpleList($record, 'indications'))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.contraindications')
                            ->label('Contraindications')
                            ->state(fn ($record): ?string => self::formatSimpleList($record, 'contraindications'))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.precautions')
                            ->label('Precautions')
                            ->state(fn ($record): ?string => self::formatSimpleList($record, 'precautions'))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.side_effects')
                            ->label('Side Effects')
                            ->state(fn ($record): ?string => self::formatSimpleList($record, 'side_effects'))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.dosages')
                            ->label('Dosages')
                            ->state(fn ($record): ?string => self::formatDosages($record))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.withdrawal_periods')
                            ->label('Withdrawal Periods')
                            ->state(fn ($record): ?string => self::formatWithdrawalPeriods($record))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.image_urls')
                            ->label('Image URLs')
                            ->state(fn ($record): ?string => self::formatSimpleList($record, 'image_urls'))
                            ->placeholder('None'),
                        TextEntry::make('submitted_data.documents')
                            ->label('Documents')
                            ->state(fn ($record): ?string => self::formatDocuments($record))
                            ->placeholder('None'),
                    ]),

            ]);
    }

    private static function formatList($record, string $key, string $displayKey): ?string
    {
        $data = $record?->submitted_data[$key] ?? null;

        if (empty($data) || ! is_array($data)) {
            return null;
        }

        return collect($data)
            ->pluck($displayKey)
            ->filter()
            ->implode(', ');
    }

    private static function formatSimpleList($record, string $key): ?string
    {
        $data = $record?->submitted_data[$key] ?? null;

        if (empty($data) || ! is_array($data)) {
            return null;
        }

        return collect($data)
            ->filter()
            ->implode("\n");
    }

    private static function formatDosages($record): ?string
    {
        $data = $record?->submitted_data['dosages'] ?? null;

        if (empty($data) || ! is_array($data)) {
            return null;
        }

        return collect($data)
            ->map(fn (array $d): string => trim(implode(' | ', array_filter([
                $d['species'] ?? null,
                $d['dosage'] ?? null,
                $d['route'] ?? null,
                $d['duration'] ?? null,
            ]))))
            ->filter()
            ->implode("\n");
    }

    private static function formatWithdrawalPeriods($record): ?string
    {
        $data = $record?->submitted_data['withdrawal_periods'] ?? null;

        if (empty($data) || ! is_array($data)) {
            return null;
        }

        return collect($data)
            ->map(fn (array $w): string => trim(implode(' | ', array_filter([
                $w['species'] ?? null,
                'Meat: '.($w['meat_days'] ?? '-'),
                'Milk: '.($w['milk_days'] ?? '-'),
                'Eggs: '.($w['egg_days'] ?? '-'),
            ]))))
            ->filter()
            ->implode("\n");
    }

    private static function formatDocuments($record): ?string
    {
        $data = $record?->submitted_data['documents'] ?? null;

        if (empty($data) || ! is_array($data)) {
            return null;
        }

        return collect($data)
            ->map(fn (array $d): string => ($d['title'] ?? 'Untitled').': '.($d['url'] ?? ''))
            ->filter()
            ->implode("\n");
    }
}
