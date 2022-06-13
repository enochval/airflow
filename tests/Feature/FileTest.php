<?php
use  \Illuminate\Support\Facades\Storage;
use \Illuminate\Http\UploadedFile;
use \App\Helpers\FileUtil;
use \App\Models\File;

uses(Tests\TestCase::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('File is uploaded to the appropriate disk', function () {
    Storage::fake('avatars');
    $dummyFile = UploadedFile::fake()->image('avatar.jpg');
    $uploaded = (new FileUtil)->uploadBlob($dummyFile, 'avatars');
    $title = $title ?? $dummyFile->getClientOriginalName();
    $shouldExist = "avatars/$title";
    Storage::assertExists($shouldExist);

    $this->assertDatabaseHas('files', [
        'disk' => config('filesystems.default'),
        'file' => $shouldExist,
        'extension' => $dummyFile->getClientOriginalExtension(),
        'file_size' => $dummyFile->getSize()
    ]);
});

test("Uploaded file is deleted", function () {
    Storage::fake('avatars');
    $dummyFile = UploadedFile::fake()->image('avatar.jpg');
    $uploaded = (new FileUtil)->uploadBlob($dummyFile, 'avatars');
    $title = $title ?? $dummyFile->getClientOriginalName();
    $shouldExist = "avatars/$title";

    $deleted = (new FileUtil)->deleteFile($uploaded->id);

    $this->assertDatabaseMissing('files', [
        'file' => $shouldExist,
        'extension' => $dummyFile->getClientOriginalExtension(),
        'file_size' => $dummyFile->getSize()
    ]);

    expect($deleted)->toBe(true);
});
