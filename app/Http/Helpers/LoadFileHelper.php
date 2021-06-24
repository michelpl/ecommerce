<?php

namespace App\Http\Helpers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class LoadFileHelper
{
    public function loadJsonFile(string $fileName)
    {
        try {
            return json_decode(Storage::disk('local')->get($fileName));
        } catch (FileNotFoundException $e) {
            return $e->getMessage();
        }
    }
}
