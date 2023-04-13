<?php

namespace App\Http\Controllers;

use App\Lib\QuranHelper;
use App\Lib\QuranReader;
use App\Lib\ResponseGenerator;
use App\Lib\YatesShuffleEngine;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use JsonMachine\Items;

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
        return ResponseGenerator::make200(QuranHelper::getAllQuranPagesCount());
    }

    public function test()
    {
    }

    public function surah_list()
    {
        $data = json_decode(file_get_contents(storage_path("app/meta.json")));

        return ResponseGenerator::make200($data);
    }

    public function surah($surah)
    {
        return $this->verse($surah);
    }

    public function verse($surah, $verse = null)
    {
        $surah = QuranHelper::parseParam($surah, range(1, 114));

        $surahJson = QuranReader::getSurah($surah);

        if (empty($verse)) {
            return ResponseGenerator::make200($surahJson);
        }

        $verse = QuranHelper::parseParam($verse, range(1, $surahJson['numberOfVerses']));

        $specificVerse = $surahJson['verses'][$verse - 1];
        $specificVerse->surah = array_diff_key($surahJson, array_flip(["verses"]));

        return ResponseGenerator::make200($specificVerse);
    }

    public function page($page)
    {
        $page = QuranHelper::parseParam($page, range(1, 604));

        return  ResponseGenerator::make200(QuranReader::getPage($page));
    }

    public function mushaf($page)
    {
        $page = QuranHelper::parseParam($page, range(1, 604));

        $prevPageUrl = null;
        $nextPageUrl = null;

        $prevPageNum = $page - 1;
        $nextPageNum = $page + 1;

        if (in_array($prevPageNum, range(1, 604))) {
            $prevPageUrl = url("v1/mushaf/$prevPageNum");
        }

        if (in_array($nextPageNum, range(1, 604))) {
            $nextPageUrl = url("v1/mushaf/$nextPageNum");
        }


        $content = QuranReader::getPage($page);

        return  ResponseGenerator::make200([
            "prev_page_url" => $prevPageUrl,
            "next_page_url" => $nextPageUrl,
            "content" => $content
        ]);
    }

    public function verifikasi_ayat()
    {
        $directory = storage_path("app/quran-data/pages");
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));

        $result = 0;
        $ayatPerPages = [];
        $ayatPerPageDetail = [];
        $count = 0;

        foreach ($scanned_directory as $file) {
            $count++;
            $filePath = storage_path("app/quran-data/pages/{$file}");

            $data = Items::fromFile($filePath);

            $arrayResponse  = iterator_to_array($data);

            $jmlAyat = count($arrayResponse);
            $ayatPerPages[] = $jmlAyat;
            $ayatPerPageDetail = [
                ...$ayatPerPageDetail,
                "Halaman $count" => $jmlAyat
            ];

            $result += $jmlAyat;
        }

        return ResponseGenerator::make200([
            "ayat_per_page" => $ayatPerPages,
            "detail" => $ayatPerPageDetail,
            "rumus" => implode(" + ", $ayatPerPages) . " = $result",
            "total" => $result
        ]);
    }
}
