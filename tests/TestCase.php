<?php

namespace Voerro\FileUploader\Test;

use Voerro\FileUploader\FileUploaderFacade;
use Voerro\FileUploader\FileUploaderServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return Voerro\FileUploader\FileUploaderServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [FileUploaderServiceProvider::class];
    }

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'FileUploader' => FileUploaderFacade::class,
        ];
    }
}
