<?php

namespace App\Filament\Resources\LargeAnimals\Findings\Pages;

use App\Filament\Resources\LargeAnimals\Findings\FindingResource;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\FindingImage;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateFinding extends CreateRecord
{
    protected static string $resource = FindingResource::class;

    /**
     * Fields that belong to `finding_images`, not `findings`.
     *
     * They arrive in the form state because Filament has already uploaded the file
     * by the time this runs, but they are not columns on the model and not in
     * `$fillable` — passing them into `create()` would throw on the first field and
     * lose the photograph along with the lesion.
     */
    private const PHOTOGRAPH_FIELDS = ['image', 'caption', 'caption_ar'];

    /**
     * Create the finding, then attach its photograph if one was uploaded.
     *
     * The order is not negotiable. Filament uploads the file during
     * `getState()`, which happens *before* this method runs, so by the time we
     * have a finding there is a file sitting in `findings/tmp/` waiting for an id
     * to be named after — and `findings.id` is an auto-increment that could not be
     * known when the admin picked the file.
     *
     * So: write the row, move the file to `findings/{id}/`, then write the image
     * row pointing at the new path. The Images tab files straight into
     * `findings/{id}/` because there the record already exists to ask; this is the
     * only place that has to do it in two steps, and it is why both paths land in
     * the same folder.
     */
    protected function handleRecordCreation(array $data): Finding
    {
        $path = $data['image'] ?? null;
        $caption = filled($data['caption'] ?? null) ? $data['caption'] : null;
        $captionAr = filled($data['caption_ar'] ?? null) ? $data['caption_ar'] : null;

        foreach (self::PHOTOGRAPH_FIELDS as $field) {
            unset($data[$field]);
        }

        $finding = static::getModel()::create($data);

        if (is_string($path) && $path !== '') {
            $this->attachPhotograph($finding, $path, $caption, $captionAr);
        }

        return $finding;
    }

    /**
     * Move the staged upload into the finding's own folder and record it.
     *
     * Every failure here is swallowed, reported, and leaves the lesion created. A
     * missing photograph is a gap an editor can fill from the Images tab in ten
     * seconds; a lesion that was not saved because its picture did not move is
     * gone. The two are not worth the same outcome.
     */
    private function attachPhotograph(Finding $finding, string $path, ?string $caption, ?string $captionAr): void
    {
        $disk = Storage::disk('public');
        $directory = 'findings/'.$finding->getKey();
        $filename = basename($path);
        $target = $directory.'/'.$filename;

        try {
            if (! $disk->exists($path)) {
                throw new \RuntimeException("uploaded file [{$path}] is not on the public disk");
            }

            if (! $disk->move($path, $target)) {
                throw new \RuntimeException("could not move [{$path}] to [{$target}]");
            }

            FindingImage::create([
                'finding_id' => $finding->getKey(),
                'image' => $target,
                'caption' => $caption,
                'caption_ar' => $captionAr,
                'sort_order' => 0,
            ]);
        } catch (Throwable $exception) {
            /**
             * The staged file is left where it is rather than deleted. It is
             * unreachable from the application, but it is also the only copy, and
             * a one-off cleanup is better than data lost to a permissions error.
             */
            Log::warning('Finding photograph could not be attached after creation.', [
                'finding_id' => $finding->getKey(),
                'staged_path' => $path,
                'exception' => $exception->getMessage(),
            ]);

            Notification::make()
                ->warning()
                ->title('The finding was saved, but its photograph was not')
                ->body('Re-upload it from the Images tab on this finding.')
                ->persistent()
                ->send();
        }
    }
}
