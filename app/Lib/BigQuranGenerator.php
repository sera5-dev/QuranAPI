<?php

namespace App\Lib;

use Josantonius\Json\Json;

class BigQuranGenerator
{
    public static function combine()
    {
        $surahLists = JsonReader::getIteratorFromAppStorage("data/meta/combined.json");

        $bigQuranJson = new Json(FileLib::createOrUnlink(storage_path("app/data/big_quran.json")));

        $bigQuranJson->set();

        foreach ($surahLists as $key => $surah) {
            $surahKey = $key + 1;

            $verses = JsonReader::getIteratorFromAppStorage("data/surah/{$surahKey}.json", [
                "pointer" => "/verses"
            ]);

            $bigQuranJson->set($surah, "$key");
            $bigQuranJson->set(iterator_to_array($verses), "{$key}.verses");
        }
    }
}
