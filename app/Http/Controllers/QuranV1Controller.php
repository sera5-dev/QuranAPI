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
                "listSurah" => url("v1/surah"),
                "pagesCount" => url("v1/pages_count"),
                "spesificPage" => [
                    "pattern" => url("v1/page/{surah}"),
                    "example" => url("v1/page/18"),
                ],
                "randomPage" => [
                    "pattern" => url("v1/page/random"),
                ],
                "spesificSurah" => [
                    "pattern" => url("v1/surah/{surah}"),
                    "example" => url("v1/surah/18"),
                ],
                "randomSurah" => [
                    "pattern" => url("v1/surah/random"),
                ],
                "spesificAyahInSurah" => [
                    "pattern" => url("v1/surah/{surah}/{ayah}"),
                    "example" => url("v1/surah/18/60"),
                ],
                "randomAyahInSurah" => [
                    "pattern" => url("v1/surah/{surah}/random"),
                    "example" => url("v1/surah/18/random"),
                ],
                "spesificAyahInRandomSurah" => [
                    "pattern" => url("v1/surah/random/{ayah}"),
                    "example" => url("v1/surah/random/60"),
                ],
                "randomAyahInRandomSurah" => [
                    "pattern" => url("v1/surah/random/random"),
                ],
                //"spesificJuz" => ["pattern" => "/juz/{juz}", "example" => "/juz/30"],
            ],
            "comment" => "This API is compatible with https://api.quran.gading.dev/.",
            "maintaner" => "Sera5 dev team <sera5@ptalmaun.com>",
            "source" => "https://github.com/sera5-dev/quran-api",
        ]);
    }

    public function pages_count()
    {
        return QuranHelper::getAllQuranPagesCount();
    }

    public function test()
    {
        return QuranHelper::getAllQuranPagesCount();
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

    public function page($page)
    {
        if ($page == "random") {
            $page = rand(1, 604);
        }

        return  ResponseGenerator::make200(QuranHelper::loadPage($page));
    }
}
