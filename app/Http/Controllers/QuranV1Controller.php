<?php

namespace App\Http\Controllers;

use App\Lib\QuranHelper;
use App\Lib\ResponseGenerator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class QuranV1Controller extends Controller
{
    public function index()
    {
        return ResponseGenerator::make200([
            "surah" => [
                "listSurah" => "/surah",
                "spesificSurah" => [
                    "pattern" => "/surah/{surah}",
                    "example" => "/surah/18",
                ],
                "spesificAyahInSurah" => [
                    "pattern" => "/surah/{surah}/{ayah}",
                    "example" => "/surah/18/60",
                ],
                //"spesificJuz" => ["pattern" => "/juz/{juz}", "example" => "/juz/30"],
            ],
            "comment" => "This API is compatible with https://api.quran.gading.dev/.",
            "maintaner" => "Sera5 dev team <sera5@ptalmaun.com>",
            "source" => "https://github.com/sera5-dev/quran-api",
        ]);
    }

    public function test()
    {
        return ResponseGenerator::make200([]);
    }

    public function surah_list()
    {
        $data = json_decode(file_get_contents(storage_path("app/meta.json")));

        return ResponseGenerator::make200($data);
    }

    public function surah($surah)
    {
        if ($surah == "random") {
            $surah = rand(1, 114);
        }

        return ResponseGenerator::make200(QuranHelper::loadSurah($surah));
    }

    public function verse($surah, $verse)
    {
        if ($surah == "random") {
            $surah = rand(1, 114);
        }

        $surah = QuranHelper::loadSurah($surah);

        if ($verse == "random") {
            $verse = rand(1,  $surah['numberOfVerses']);
        } else {
            if (!in_array($verse, range(1, $surah['numberOfVerses']))) {
                $verse = 1;
            }
        }

        $specificVerse = $surah['verses'][$verse - 1];
        $specificVerse->surah = array_diff_key($surah, array_flip(["verses"]));

        return ResponseGenerator::make200($specificVerse);
    }
}
