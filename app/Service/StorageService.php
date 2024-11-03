<?php

namespace App\Service;

use App\Exceptions\BadRequestException;
use Illuminate\Support\Facades\Storage;

class StorageService
{

    /**
     * @param $file
     * @param $path
     * @return bool|int
     */
    public static function storeLocally($file, $path): bool|int
    {
        $path = storage_path("app/" . $path);
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        return file_put_contents($path, file_get_contents($file));
    }

    /**
     * @throws BadRequestException
     */
    public static function storeCloud($path): bool
    {
        $localPath = storage_path("app/" . $path);
        $fileContents = file_get_contents($localPath);

        if ($fileContents === false) {
            throw new BadRequestException("There is no such file in path " . $localPath, 400);
        }

        return Storage::put($path, $fileContents);
    }

    /**
     * @param $path
     * @return bool
     */
    public static function removeLocalFile($path): bool
    {
        $localPath = storage_path("app/" . $path);
        return unlink($localPath);
    }
}
