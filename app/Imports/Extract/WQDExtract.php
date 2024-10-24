<?php

namespace App\Imports\Extract;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WQDExtract
{
    /**
     * @param $prefix
     * @return array
     */
    public static function read($prefix): array
    {
        $wqdPath = env('WQD_PATH') . "/" . $prefix;
        $directoryPath = storage_path($wqdPath);
        if (File::exists($directoryPath)) {
            $files = File::allFiles($directoryPath);
            if(sizeof($files) == 0){
                return [];
            }
            $lastModifiedFile = collect($files)->sortByDesc(function ($file) {
                return $file->getCTime();
            })->first();

            return [
                "file" => File::get($lastModifiedFile),
                "latestFileName" => $lastModifiedFile->getFilename()
            ];
        }
        return [];
    }
}
