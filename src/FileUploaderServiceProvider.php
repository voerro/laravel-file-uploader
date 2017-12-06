<?php

namespace Voerro\FileUploader;

use Illuminate\Support\ServiceProvider;

class FileUploaderServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FileUploader::class, function () {
            return new FileUploader();
        });

        $this->app->alias(FileUploader::class, 'laravel-file-uploader');
    }
}
