<?php

namespace App\Http\Controllers;

use App\Lib\QuranHelper;
use App\Lib\ResponseGenerator;
use App\Lib\YatesShuffleEngine;
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
            "maintaner" => "Sera5 dev team <sera5@ptalmaun.com>, asbp <1177050008@student.uinsgd.ac.id>",
            "source" => "https://github.com/sera5-dev/QuranAPI",
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
        $range = range(1, 114);

        if ($surah == "random") {
            $topYates = YatesShuffleEngine::get_top_shuffle($range, 3);
            $surah = reset($topYates);
        } elseif ($surah == "first") {
            $surah = $range[array_key_first($range)];
        } elseif ($surah == "last") {
            $surah = $range[array_key_last($range)];
        } else {
            if (!in_array($surah, $range)) {
                return $this->surah("random");
            }
        }

        return ResponseGenerator::make200(QuranHelper::loadSurah($surah));
    }

    public function verse($surah, $verse)
    {
        $range = range(1, 114);

        //Parse surah first
        if ($surah == "random") {
            $topYates = YatesShuffleEngine::get_top_shuffle($range, 3);
            $surah = reset($topYates);
        } elseif ($surah == "first") {
            $surah = $range[array_key_first($range)];
        } elseif ($surah == "last") {
            $surah = $range[array_key_last($range)];
        } else {
            if (!in_array($surah, $range)) {
                return $this->verse("random", "random");
            }
        }

        $surahJson = QuranHelper::loadSurah($surah);
        $verseRange = range(1, $surahJson['numberOfVerses']);

        // why we parse the verse last? because we have to get
        // number of verses first.
        if ($verse == "random") {
            $topYates = YatesShuffleEngine::get_top_shuffle($verseRange, 3);
            $verse = reset($topYates);
        } elseif ($verse == "first") {
            $verse = $verseRange[array_key_first($verseRange)];
        } elseif ($verse == "last") {
            $verse = $verseRange[array_key_last($verseRange)];
        } else {
            if (!in_array($verse, $verseRange)) {
                return $this->verse($surah, "random");
            }
        }

        $specificVerse = $surahJson['verses'][$verse - 1];
        $specificVerse->surah = array_diff_key($surahJson, array_flip(["verses"]));

        return ResponseGenerator::make200($specificVerse);
    }

    public function page($page)
    {
        $range = range(1, 604);

        if ($page == "random") {
            $topYates = YatesShuffleEngine::get_top_shuffle($range, 3);
            $page = reset($topYates);
        } elseif ($page == "first") {
            $page = $range[array_key_first($range)];
        } elseif ($page == "last") {
            $page = $range[array_key_last($range)];
        } else {
            if (!in_array($page, $range)) {
                return $this->page("random");
            }
        }

        return  ResponseGenerator::make200(QuranHelper::loadPage($page));
    }
}
