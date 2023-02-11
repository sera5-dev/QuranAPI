<?php

namespace App\Lib;

class JsonReader
{
    public static function readJsonFile($name)
    {
        $data = json_decode(file_get_contents($name));
        return $data;
    }

    public static function readJsonFileFromAppStorage($name)
    {
        return static::readJsonFile(storage_path("app/$name"));
    }
}
