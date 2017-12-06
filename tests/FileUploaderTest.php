<?php

namespace Voerro\FileUploader\Test;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Voerro\FileUploader\FileUploader;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploaderTest extends TestCase
{
    // TODO;
    // replace file
    // replace file as

    public function testItUploadsFiles()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('file.pdf', 100);

        FileUploader::make($file)->upload();

        Storage::disk('public')->assertExists($file->hashName());

        $path = FileUploader::make($file)->upload('documents');

        $this->assertEquals('documents/' . $file->hashName(), $path);
        Storage::disk('public')->assertExists($path);
    }

    public function testItUploadsFilesUnderSpecifiedNames()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('file.pdf', 100);

        $filename = 'document';

        $path = FileUploader::make($file)->uploadAs($filename);

        $this->assertEquals("{$filename}.pdf", $path);
        Storage::disk('public')->assertExists($path);
    }

    public function testItDownsizesImageBeforeUploading()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('image.jpg', 640, 480);

        $path = FileUploader::make($image)->downsize(200, 200)->upload();

        Storage::disk('public')->assertExists($path);

        $uploaded = Image::make(Storage::disk('public')->get($path));
        $this->assertLessThanOrEqual(200, $uploaded->width());
        $this->assertLessThanOrEqual(200, $uploaded->height());
    }
}
