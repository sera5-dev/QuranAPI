<?php

namespace App\Lib;

use JsonMachine\Items;

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

    public static function readFromStorage($path)
    {
        $items = Items::fromFile(storage_path("app/$path"));
        return iterator_to_array($items);
    }

    public static function getIteratorFromAppStorage($path, $options = [])
    {
        return Items::fromFile(storage_path("app/$path"), $options);
    }

    public static function getIteratorFromUrl($url, $pointer = "/", $method = "GET")
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request($method, $url);
        $phpStream = \GuzzleHttp\Psr7\StreamWrapper::getResource($response->getBody());

        return Items::fromStream($phpStream, ["pointer" => $pointer]);
    }

    public static function readFromUrl($url, $pointer = "/", $method = "GET")
    {
        return iterator_to_array(static::getIteratorFromUrl($url, $pointer, $method));
    }
}
