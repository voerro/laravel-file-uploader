# Laravel File Uploader

[![Packagist](https://img.shields.io/packagist/dt/voerro/laravel-file-uploader.svg?style=flat-square)](https://packagist.org/packages/voerro/laravel-file-uploader) [![Packagist](https://img.shields.io/packagist/l/voerro/laravel-file-uploader.svg?style=flat-square)](https://opensource.org/licenses/MIT)

A very simple helper class that makes these tasks a walk in the park:
- replacing an old/existing file with a new one
- downsizing an image on upload
- deleting a file from a storage
- determining if a file is an image

## Installation

Via composer:

```
composer require voerro/laravel-file-uploader
```

#### For Laravel Below 5.5

Add the `FileUploaderServiceProvider` to the `providers` array of your `config/app.php` configuration file:

```php
Voerro\FileUploader\FileUploaderServiceProvider::class,
```

Then add the `Facade` to the `aliases` array:

```php
'FileUploader' => Voerro\FileUploader\FileUploaderFacade::class,
```

## Basic Usage

Import the `FileUploader` class like this:

```php
use Voerro\FileUploader\FileUploader;
```

Pass the uploaded file (an `Illuminate\Http\UploadedFile`) instance to the `make` method, then chain the `upload` method, which will return the path to the newly stored file.

```php
$path = FileUploader::make($file)->upload();
```

## Methods

#### Static methods

Initialization:

```php
::make(Illuminate\Http\UploadedFile $file)
```

Deleting a file (the method checks if the file exists to eliminate possible errors):

```php
::delete(string $filePath, string $storage = 'public')
```

Determine if a file is an image. Pass to the method an UploadedFile instance or a string with a path to the file:

```php
::isImage($file, $storage = 'public')
```

#### The following methods should be chained after the `make` method.

```php
->upload(string $path = '', string $storage = 'public')
```

Upload file under a specified name:

```php
->uploadAs(string $filename, string $path = '', string $storage = 'public')
```

Replace an old file:

```php
->replace(string $oldFilePath, string $path = '', string $storage = 'public')
```

Replace an old file, store the new file under a specified name:

```php
->replaceAs(string $oldFilePath, string $newFilename, string $path = '', string $storage = 'public')
```

Downsize an image if it's bigger than the specified width and/or height (the aspect ratio will be saved). When called on a non-image file nothing would be happen, thus you don't need to manually check if the file is an image before deciding wether to call this method.

```php
->downsize(integer $maxWidth, integer $maxHeight)
```

Call this method before calling any of the above methods, for example:

```php
FileUploader::make($image)->downsize(200, 200)->replace('old_image_file.jpg');
```

## License

This is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
