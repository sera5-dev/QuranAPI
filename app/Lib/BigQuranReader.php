<?php

namespace App\Lib;

use Josantonius\Json\Json;
use JsonMachine\Items;

class BigQuranReader
{
    public static function loadSurah($surah)
    {
        $surahKey = $surah - 1;
        $items = JsonReader::getIteratorFromAppStorage("data/big_quran.json", [
            "pointer" => "/$surahKey"
        ]);

        return iterator_to_array($items);
    }
}
