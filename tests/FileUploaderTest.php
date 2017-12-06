<?php

namespace Voerro\FileUploader\Test;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Voerro\FileUploader\FileUploader;

class FileUploaderTest extends TestCase
{
    // TODO;
    // downsizing an image before uploading
    // replace file
    // replace file as

    public function testItUploadsFiles()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('file.pdf', 100);

        FileUploader::make($file)->upload();

        Storage::disk('public')->assertExists($file->hashName());

        FileUploader::make($file)->upload('documents/');

        Storage::disk('public')->assertExists('documents/' . $file->hashName());
    }

    public function testItUploadsFilesUnderSpecifiedNames()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('file.pdf', 100);

        $filename = 'document';

        FileUploader::make($file)->uploadAs($filename);

        Storage::disk('public')->assertExists("{$filename}.pdf");
    }
}
