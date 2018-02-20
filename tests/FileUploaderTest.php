<?php

namespace Voerro\FileUploader\Test;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Voerro\FileUploader\FileUploader;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploaderTest extends TestCase
{
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

        $filename = 'document.pdf';

        $path = FileUploader::make($file)->uploadAs($filename);

        $this->assertEquals($filename, $path);
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

    public function testItDownsizesAndCropsImageBeforeUploading()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('image.jpg', 640, 480);

        $path = FileUploader::make($image)->fit(640, 640)->upload();

        Storage::disk('public')->assertExists($path);

        $uploaded = Image::make(Storage::disk('public')->get($path));
        $this->assertEquals(640, $uploaded->width());
        $this->assertEquals(640, $uploaded->height());
    }

    public function testItDownsizesAndCropsImageBeforeUploadingWithoutUpscaling()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('image.jpg', 640, 480);

        $path = FileUploader::make($image)->fit(640, 640, true)->upload();

        Storage::disk('public')->assertExists($path);

        $uploaded = Image::make(Storage::disk('public')->get($path));
        $this->assertEquals(480, $uploaded->width());
        $this->assertEquals(480, $uploaded->height());
    }

    public function testItReplacesTheOldFileWithTheNewFile()
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->create('file.pdf', 100);

        $oldPath = FileUploader::make($oldFile)->upload();

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->create('new_file.pdf', 100);

        $newPath = FileUploader::make($newFile)->replace($oldPath);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function testItReplacesTheOldFileWithTheNewFileUnderSpecifiedName()
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->create('file.pdf', 100);

        $oldPath = FileUploader::make($oldFile)->upload();

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->create('new_file.pdf', 100);

        $newFilename = 'new_document.pdf';
        $newPath = FileUploader::make($newFile)->replaceAs($oldPath, $newFilename);

        Storage::disk('public')->assertMissing($oldPath);

        $this->assertEquals($newFilename, $newPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function testItCanDetermineIfFileIsImage()
    {
        $file = UploadedFile::fake()->create('file.pdf', 100);
        $image = UploadedFile::fake()->image('iamge.jpg', 100, 100);

        $this->assertTrue(FileUploader::isImage($image));
        $this->assertFalse(FileUploader::isImage($file));

        $filePath = FileUploader::make($file)->upload();
        $imagePath = FileUploader::make($image)->upload();

        $this->assertTrue(FileUploader::isImage($imagePath));
        $this->assertFalse(FileUploader::isImage($filePath));
    }

    public function testNothingBreaksWhenItPerformDownsizeOnNonImage()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('file.pdf', 100);

        $path = FileUploader::make($file)->downsize(50, 50)->upload();

        Storage::disk('public')->assertExists($path);
    }
}
