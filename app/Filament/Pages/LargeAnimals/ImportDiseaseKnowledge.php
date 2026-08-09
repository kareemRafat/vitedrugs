<?php

namespace App\Filament\Pages\LargeAnimals;

use App\Filament\Clusters\Diseases\DiseasesCluster;
use App\Models\Disease;
use App\Services\Knowledge\DiseaseKnowledgePayloadTransformer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use JsonException;

class ImportDiseaseKnowledge extends Page
{
    protected static ?string $cluster = DiseasesCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?string $navigationLabel = 'Import Disease Knowledge';

    protected string $view = 'filament.pages.large-animals.import-disease-knowledge';

    protected ?string $heading = 'Import Disease Knowledge';

    protected ?string $subheading = 'Upload a JSON payload onto a Disease\'s knowledge base';

    public function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import JSON')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Select::make('disease_id')
                        ->label('Disease')
                        ->options(fn (): array => Disease::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Select the Disease to receive this knowledge payload.'),

                    FileUpload::make('json_file')
                        ->label('JSON File')
                        ->disk('local')
                        ->directory('knowledge-imports')
                        ->acceptedFileTypes(['application/json', 'text/plain', 'application/octet-stream'])
                        ->maxSize(2048)
                        ->helperText('Upload a .json file containing the knowledge payload.'),

                    Textarea::make('json')
                        ->label('JSON Payload')
                        ->rows(12)
                        ->helperText('Expected keys: clinical_signs, postmortem_findings, diagnosis, treatment, prevention_control, references. A rich disease document wrapped in a "disease" key (clinical_manifestations, diagnostic_methods, ...) is also accepted and mapped automatically. Provide either a file or pasted text.'),
                ])
                ->action(function (array $data): void {
                    $payload = $this->resolvePayload($data['json'] ?? null, $data['json_file'] ?? null);

                    if ($payload === null) {
                        Notification::make()
                            ->danger()
                            ->title('No valid JSON provided')
                            ->body('Provide a valid JSON file or pasted text with the expected payload keys.')
                            ->send();

                        return;
                    }

                    Disease::find($data['disease_id'])?->update([
                        'knowledge_payload' => $payload,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Knowledge imported')
                        ->body('The knowledge payload was saved to the selected Disease.')
                        ->send();
                }),
        ];
    }

    protected function resolvePayload(?string $jsonText, mixed $jsonFile): ?array
    {
        $raw = null;

        if (is_string($jsonFile) && filled($jsonFile)) {
            $raw = Storage::disk('local')->get($jsonFile);
        } elseif (is_array($jsonFile) && isset($jsonFile[0]['path'])) {
            $raw = Storage::disk('local')->get($jsonFile[0]['path']);
        }

        if ($raw === null && is_string($jsonText) && trim($jsonText) !== '') {
            $raw = $jsonText;
        }

        if ($raw === null) {
            return null;
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        if (! is_array($decoded)) {
            return null;
        }

        return DiseaseKnowledgePayloadTransformer::transform($decoded);
    }
}
