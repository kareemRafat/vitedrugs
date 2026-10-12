<?php

namespace App\Filament\Pages\Diseases;

use App\Filament\Clusters\Diseases\DiseasesCluster;
use App\Models\Disease;
use App\Services\Knowledge\DiseaseKnowledgePayloadTransformer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JsonException;

class ImportDiseaseKnowledge extends Page
{
    protected static ?string $cluster = DiseasesCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?string $navigationLabel = 'Import Disease Knowledge';

    protected static ?string $title = 'Import Disease Knowledge';

    protected string $view = 'filament.pages.diseases.import-disease-knowledge';

    protected ?string $heading = 'Import Disease Knowledge';

    protected ?string $subheading = 'Upload JSON knowledge payloads onto a Disease\'s knowledge base';

    public function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import JSON')
                ->icon('heroicon-o-arrow-up-tray')
                ->schema($this->schema())
                ->action(fn (array $data) => $this->handleSingle($data)),

            Action::make('importBulk')
                ->label('Bulk import')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('Bulk import knowledge files')
                ->modalDescription('Select every disease JSON file at once. Each file is matched to a Disease by its filename, so "newcastle-disease.json" fills the "newcastle-disease" Disease.')
                ->schema($this->bulkSchema())
                ->action(fn (array $data) => $this->handleBulk($data)),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function schema(): array
    {
        return [
            Select::make('domain')
                ->label('Section')
                ->options($this->domainOptions())
                ->default(Disease::DOMAIN_LARGE_ANIMAL)
                ->required()
                ->live(),

            Select::make('disease_id')
                ->label('Disease')
                ->options(fn (Get $get): array => $this->diseaseOptions($get('domain')))
                ->searchable()
                ->preload()
                ->required()
                ->helperText('Select the Disease to receive this knowledge payload.'),

            FileUpload::make('json_file')
                ->label('JSON File')
                ->disk('local')
                ->directory('knowledge-imports')
                ->preserveFilenames()
                ->acceptedFileTypes(['application/json', 'text/plain', 'application/octet-stream'])
                ->maxSize(2048)
                ->helperText('Upload a .json file containing the knowledge payload.'),

            Textarea::make('json')
                ->label('JSON Payload')
                ->rows(12)
                ->helperText('Expected keys: clinical_signs, postmortem_findings, diagnosis, treatment, prevention_control, references. A rich disease document wrapped in a "disease" key (clinical_manifestations, diagnostic_methods, ...) is also accepted and mapped automatically. Provide either a file or pasted text.'),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function bulkSchema(): array
    {
        return [
            Select::make('domain')
                ->label('Section')
                ->options($this->domainOptions())
                ->default(Disease::DOMAIN_POULTRY)
                ->required(),

            FileUpload::make('files')
                ->label('Knowledge files')
                ->disk('local')
                ->directory('knowledge-imports')
                // The filename is what links a file to a Disease, so it has to
                // survive storage; without this Filament rewrites it to a UUID.
                ->preserveFilenames()
                ->multiple()
                ->acceptedFileTypes(['application/json', 'text/plain', 'application/octet-stream'])
                ->maxSize(4096)
                ->maxFiles(400)
                ->required()
                ->helperText('Select every .json file you want to load in one go.'),

            Toggle::make('preview_only')
                ->label('Preview only')
                ->helperText('Report what would be matched and written, without saving anything.')
                ->default(false),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function domainOptions(): array
    {
        return [
            Disease::DOMAIN_LARGE_ANIMAL => 'Large Animals',
            Disease::DOMAIN_POULTRY => 'Poultry',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function diseaseOptions(?string $domain): array
    {
        return Disease::query()
            ->when($domain, fn ($query) => $query->where('domain', $domain))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleSingle(array $data): void
    {
        $payload = $this->decode($this->readUpload($data['json_file'] ?? null) ?? ($data['json'] ?? null));

        if ($payload === null) {
            Notification::make()
                ->danger()
                ->title('No valid JSON provided')
                ->body('Provide a valid JSON file or pasted text with the expected payload keys.')
                ->send();

            return;
        }

        Disease::find($data['disease_id'])?->update(['knowledge_payload' => $payload]);

        Notification::make()
            ->success()
            ->title('Knowledge imported')
            ->body('The knowledge payload was saved to the selected Disease.')
            ->send();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleBulk(array $data): void
    {
        $paths = $this->readUploads($data['files'] ?? null);
        $domain = $data['domain'] ?? Disease::DOMAIN_POULTRY;
        $previewOnly = (bool) ($data['preview_only'] ?? false);

        if ($paths === []) {
            Notification::make()->danger()->title('No files received')->send();

            return;
        }

        $matched = [];
        $unmatched = [];

        foreach ($paths as $path) {
            $disease = $this->matchDisease($path, $domain);

            if ($disease === null) {
                $unmatched[] = basename($path);

                continue;
            }

            $payload = $this->decode($this->readPath($path));

            if ($payload === null) {
                $unmatched[] = basename($path).' (invalid JSON)';

                continue;
            }

            $matched[] = ['disease' => $disease, 'payload' => $payload];
        }

        if ($previewOnly) {
            $this->report($matched, $unmatched, true);

            return;
        }

        if ($matched !== []) {
            DB::transaction(function () use ($matched): void {
                foreach ($matched as $row) {
                    $row['disease']->update(['knowledge_payload' => $row['payload']]);
                }
            });
        }

        $this->report($matched, $unmatched, false);
    }

    /**
     * Resolve an uploaded file to the Disease it describes.
     *
     * Matching runs on the filename, so the original name has to survive the
     * upload — hence preserveFilenames() on the field. Documents that are
     * worded differently from their disease go through the configured alias
     * list; anything still unresolved is reported rather than guessed at.
     */
    protected function matchDisease(string $path, string $domain): ?Disease
    {
        $stem = Str::slug(pathinfo($path, PATHINFO_FILENAME));

        $aliases = (array) config('vetpedia.poultry.knowledge_file_aliases', []);

        foreach ([$stem, $aliases[$stem] ?? null] as $candidate) {
            if ($candidate === null) {
                continue;
            }

            $disease = Disease::query()
                ->where('domain', $domain)
                ->where(fn ($query) => $query->where('slug', $candidate)->orWhere('name', $candidate))
                ->first();

            if ($disease !== null) {
                return $disease;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array{disease: Disease, payload: array<string, mixed>}>  $matched
     * @param  array<int, string>  $unmatched
     */
    protected function report(array $matched, array $unmatched, bool $previewOnly): void
    {
        $verb = $previewOnly ? 'would be written' : 'written';

        $notification = Notification::make()
            ->title(($previewOnly ? 'Preview: ' : '').count($matched)." payloads {$verb}, ".count($unmatched).' unmatched')
            ->success();

        if ($unmatched !== []) {
            $notification = $notification
                ->warning()
                ->body('Unmatched (nothing written): '.implode(', ', array_slice($unmatched, 0, 25))
                    .(count($unmatched) > 25 ? ' … and '.(count($unmatched) - 25).' more' : ''));
        }

        $notification->send();
    }

    protected function readUpload(mixed $upload): ?string
    {
        if (is_string($upload) && filled($upload)) {
            return $this->readPath($upload);
        }

        if (is_array($upload) && is_string($upload[0] ?? null)) {
            return $this->readPath($upload[0]);
        }

        if (is_array($upload) && is_string($upload[0]['path'] ?? null)) {
            return $this->readPath($upload[0]['path']);
        }

        return null;
    }

    /**
     * Flatten a multi-file upload into an ordered list of stored paths.
     *
     * @return array<int, string>
     */
    protected function readUploads(mixed $uploads): array
    {
        if (is_string($uploads) && filled($uploads)) {
            return [$uploads];
        }

        if (! is_array($uploads)) {
            return [];
        }

        $paths = [];

        foreach ($uploads as $key => $upload) {
            if (is_string($key) && filled($key)) {
                $paths[] = $key;

                continue;
            }

            if (is_string($upload)) {
                $paths[] = $upload;

                continue;
            }

            if (is_string($upload['path'] ?? null)) {
                $paths[] = $upload['path'];
            }
        }

        return array_values(array_filter($paths));
    }

    protected function readPath(string $path): ?string
    {
        return Storage::disk('local')->exists($path)
            ? Storage::disk('local')->get($path)
            : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function decode(mixed $raw): ?array
    {
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        return is_array($decoded) ? DiseaseKnowledgePayloadTransformer::transform($decoded) : null;
    }
}
