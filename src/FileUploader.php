<?php

namespace Voerro\FileUploader;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

class FileUploader
{
    /**
     * $file an instance of UploadedFile
     *
     * @var Illuminate\Http\UploadedFile
     */
    protected $file;

    /**
     * $image an instance of Intervention Image
     *
     * @var Intervention\Image\Facades\Image
     */
    protected $image;

    /**
     * Custom constructor
     *
     * @param Illuminate\Http\UploadedFile $file
     * @return Voerro\FileUploader\FileUploader
     */
    public static function make($file)
    {
        return (new FileUploader())->setFile($file);
    }

    /**
     * Sets the $file property
     *
     * @param Illuminate\Http\UploadedFile $file
     * @return Voerro\FileUploader\FileUploader $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Uploads a file under a hashname
     *
     * @param string $path path to upload to
     * @param string $storage
     * @return string uploaded file's path
     */
    public function upload($path = '', $storage = 'public')
    {
        return $this->uploadAs('', $path, $storage);
    }

    /**
     * Uploads a file under a speciifed name
     *
     * @param string $filename filename to upload the file under
     * @param string $path path to upload to
     * @param string $storage
     * @return string uploaded file's path
     */
    public function uploadAs($filename, $path = '', $storage = 'public')
    {
        $filename = $filename
            ? $filename . '.' . $this->file->getClientOriginalExtension()
            : $this->file->hashName();

        if ($this->image) {
            $imagePath = $path . $filename;

            $this->image->save(Storage::disk($storage)->path($imagePath));

            return $imagePath;
        } else {
            return $this->file->storeAs($path, $filename, $storage);
        }
    }

    /**
     * Replace an old file with a new one
     *
     * @param string $oldFilePath path of the file to replace
     * @param string $path path to upload the new file to
     * @param string $storage
     * @return string uploaded file's path
     */
    public function replace($oldFilePath, $path = '', $storage = 'public')
    {
        return $this->replaceAs($oldFilePath, '', $path, $storage);
    }

    /**
     * Replace an old file with a new one and store it under a specified name
     *
     * @param string $oldFilePath path of the file to be replaced
     * @param string $newFilename filename to upload the new file under
     * @param string $path path to upload the new file to
     * @param string $storage
     * @return string uploaded file's path
     */
    public function replaceAs($oldFilePath, $newFilename, $path = '', $storage = 'public')
    {
        self::delete($oldFilePath, $storage);

        return $this->uploadAs($newFilename, $path, $storage);
    }

    /**
     * Downsize an image if it's bigger than the dimensions provided
     *
     * @param integer $maxWidth maximum allowed width
     * @param integer $maxHeight maximum allowed height
     * @return Voerro\FileUploader\FileUploader $this
     */
    public function downsize($maxWidth, $maxHeight)
    {
        $image = Image::make($this->file);

        // Downsize the image if it's bigger than desired
        if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
            });

            $this->image = $image;
        }

        return $this;
    }

    /**
     * Delete a file from the specified storage
     *
     * @param string $filePath relative path to the file to delete
     * @param string $storage
     * @return void
     */
    public static function delete($filePath, $storage = 'public')
    {
        if (Storage::disk($storage)->exists($filePath)) {
            Storage::disk($storage)->delete($filePath);
        }
    }
}
