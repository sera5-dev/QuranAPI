<?php

namespace App\Lib;

class JsonLib
{
    public static function writeToJsonFile($storagePath, $content)
    {
        file_put_contents(storage_path($storagePath), json_encode($content), LOCK_EX);
    }

    public static function urlFetcherToJsonFile($ufInstance, $storagePath)
    {
        $res = $ufInstance->process();

        $response = json_decode($res);

        static::writeToJsonFile($storagePath, $response);
    }
}
