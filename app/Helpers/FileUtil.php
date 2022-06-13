<?php

namespace App\Helpers;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUtil
{
    public function uploadBlob(UploadedFile $file, string $path, ?string $title = null): File
    {
        $disk = config('filesystems.default');
        $title = $title ?? $file->getClientOriginalName();
        $uploaded = Storage::disk($disk)->putFileAs($path, $file, $title, 'public');

        return File::create([
            'disk' => $disk,
            'file' => $uploaded,
            'extension' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize()
        ]);
    }

    public function deleteFile(int $fileId): bool
    {
        $file = File::find($fileId);

        if ($file) {
            if (Storage::disk($file->disk)->exists($file->file)) {
                Storage::disk($file->disk)->delete($file->file);
            }

            return $file->delete();
        }

        return false;
    }

    public function fileSchema(File $file): array
    {
        return [
            'file_id' => $file->id,
            'url' => $this->generateFileUrl($file)
        ];
    }

    public function getFile(int $fileId): array
    {
        $file = File::find($fileId);

        return $this->fileSchema($file);
    }

    public function generateFileUrl(File $file): string
    {
        return Storage::disk($file->disk)->url($file->file);
    }
}
