<?php

namespace App\Lib;

use JsonMachine\Items;

class QuranMetaParser
{
    public static function getMainMetaParser()
    {
        return Items::fromFile(storage_path("app/data/meta/quran.com.json"), ['pointer' => '/chapters']);
    }

    public static function getCombinedMeta($chapter)
    {

        return static::getSurahMetaFiles("app/data/meta/combined.json", "/$chapter");
    }

    public static function getKemenagMeta($chapter)
    {

        return static::getSurahMeta("app/data/meta/kemenag.json", $chapter);
    }

    public static function getEQuranMeta($chapter)
    {

        return static::getSurahMeta("app/data/meta/equran.json", $chapter);
    }

    public static function getQuranComMeta($chapter)
    {
        $chapter = $chapter > 0 ? ($chapter - 1) : 0;

        return static::getSurahMeta("app/data/meta/quran.com.json", "/chapters/$chapter");
    }

    public static function getQuranCloudMeta($pointer)
    {
        $items = Items::fromFile(storage_path("app/data/meta/alquran.cloud.json"), ['pointer' => $pointer]);

        return iterator_to_array($items);
    }

    public static function getSurahMetaFiles($file, $pointer)
    {
        $items = Items::fromFile(storage_path($file), ['pointer' => $pointer]);

        return iterator_to_array($items);
    }

    public static function getSurahMeta($file, $chapter)
    {
        $chapter = $chapter > 0 ? ($chapter - 1) : 0;

        $items = Items::fromFile(storage_path($file), ['pointer' => "/data/$chapter"]);

        return iterator_to_array($items);
    }
}
