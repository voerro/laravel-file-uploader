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
     * @return string uploaded file's path
     */
    public function upload($path = '', $storage = 'public')
    {
        return $this->uploadAs('', $path, $storage);
    }

    /**
     * Uploads a file under a speciifed name
     *
     * @param string $path path to upload to
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
}
