<?php

namespace App\Http\Helpers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Throw_;

class LoadFileHelper
{
    public static array $file;

    public function __construct()
    {
    }

    public static function getJsonFile(string $fileName) : array
    {
        if (!isset(self::$file)) {
            self::$file = self::loadFile($fileName);
        }

        return self::$file;
    }

    private static function loadFile(string $fileName): array
    {
        try {
            $fileContent = Storage::disk('local')->get($fileName);

            return self::formatProductList(json_decode($fileContent));

        } catch (FileNotFoundException $e) {
            Log::critical($e->getMessage());
        }
    }

    /**
     * @param $productList
     * @return array
     */
    private static function formatProductList($productList): array
    {
        $newProductList = [];
        foreach ($productList as $product)
        {
            $newProductList[$product->id] = $product;
        }

        return $newProductList;
    }

}
