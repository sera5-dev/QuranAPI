<?php

namespace App\Lib;

use JsonMachine\Items;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class QuranReader
{
    public static function indexSurah()
    {
    }

    public static function getSurah($surah, $ayah = null)
    {
        $surahIterator = Items::fromFile(storage_path("app/data/surah/$surah.json"));

        return iterator_to_array($surahIterator);
    }

    public static function getPage($page)
    {
        $filePath = storage_path("app/data/pages/{$page}.json");

        if (!file_exists($filePath)) throw new FileNotFoundException("Halaman yang Anda cari tidak ditemukan.");

        $data = Items::fromFile($filePath);
        $array = iterator_to_array($data);

        foreach ($array as $verse) {
            $surahDetail = QuranMetaParser::getCombinedMeta($verse->meta->surah - 1);

            $verse->surah = $surahDetail;
        }

        return $array;
    }
}
