<?php

namespace App\Http\Helpers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Throw_;

class LoadFileHelper
{
    private static string $filename = "PRODUCT_LIST_JSON";

    public static $instance;


    private function __construct() {
        //
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = self::loadFile();
        }

        return self::$instance;
    }

    private static function loadFile() {
        try {
            $fileName = Env::get(self::$filename);
            $fileContent = Storage::disk('local')->get($fileName);

            return json_decode($fileContent);

        } catch (FileNotFoundException $e) {
            return $e->getMessage();
        }
    }
}
