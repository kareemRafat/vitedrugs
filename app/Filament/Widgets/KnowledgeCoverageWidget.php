<?php

namespace App\Filament\Widgets;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\LargeAnimals\MedicalArticle;
use App\Models\LargeAnimals\Microorganism;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class KnowledgeCoverageWidget extends TableWidget
{
    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        $diseaseCount = Disease::count();
        $knowledgeCount = Disease::whereNotNull('knowledge_payload')->count();

        return $table
            ->heading('Knowledge Coverage')
            ->records(fn (): array => [
                ['icon' => 'heroicon-o-heart', 'label' => 'Clinical Signs', 'count' => ClinicalSign::count()],
                ['icon' => 'heroicon-o-beaker', 'label' => 'Microorganisms', 'count' => Microorganism::count()],
                ['icon' => 'heroicon-o-globe-alt', 'label' => 'Host Species', 'count' => HostSpecies::count()],
                ['icon' => 'heroicon-o-document-text', 'label' => 'Medical Articles', 'count' => MedicalArticle::count()],
                ['icon' => 'heroicon-o-bug-ant', 'label' => 'Diseases (total)', 'count' => $diseaseCount],
                ['icon' => 'heroicon-o-check-circle', 'label' => 'Diseases with knowledge payload', 'count' => $knowledgeCount],
                ['icon' => 'heroicon-o-sparkles', 'label' => 'Knowledge coverage', 'count' => $diseaseCount > 0 ? round(($knowledgeCount / $diseaseCount) * 100).'%' : '0%'],
            ])
            ->paginated(false)
            ->columns([
                TextColumn::make('label')
                    ->label('Metric')
                    ->icon(fn (array $record): string => $record['icon'])
                    ->searchable(),
                TextColumn::make('count')
                    ->label('Value')
                    ->sortable()
                    ->alignEnd(),
            ]);
    }
}
