<?php

namespace Voerro\FileUploader;

use Illuminate\Support\Facades\Facade;

class FileUploaderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-file-uploader';
    }
}
