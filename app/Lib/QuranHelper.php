<?php

namespace App\Lib;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;

class QuranHelper
{

    public static function crawl()
    {
        $surahs = [1];
    }

    public static function fetchSurahsFromQuranCom($languages = ['id', 'en'])
    {
        foreach ($languages as $langItem) {
            $uf = new UrlFetcher(env('APP_CRAWL_BASEURL2'));

            $uf->buildGetQuery([
                "language" => $langItem
            ]);

            $res = $uf->process();

            $response = json_decode($res);

            $data = $response->chapters;

            file_put_contents(storage_path("app/meta_qurancom.$langItem.json"), json_encode($data), LOCK_EX);
        }
    }

    public static function parseResponse($res)
    {
        $response = json_decode($res);

        $data = $response->data;

        file_put_contents(storage_path("app/meta.json"), json_encode($data), LOCK_EX);
    }

    public static function readMeta($langItem)
    {
        $data = json_decode(file_get_contents(storage_path("app/meta_qurancom.$langItem.json")));
        return $data;
    }

    public static function parseMeta()
    {
        $dataEn = static::readMeta('en');
        $dataId = static::readMeta('id');

        $listOfSurahs = array_map(function ($enData, $idData) {

            if ($enData->revelation_place == "makkah") {
                $relevation = ["arab" => "مكة", "en" => "Meccan", "id" => "Makkiyyah"];
            } else {
                $relevation = ["arab" => "مدينة", "en" => "Medinan", "id" => "Madaniyyah"];
            }

            return [
                "number" => $enData->id,
                "sequence" => $enData->revelation_order,
                "numberOfVerses" => $enData->verses_count,
                "name" => [
                    "short" => $enData->name_arabic,
                    "long" => $enData->name_arabic,
                    "transliteration" => [
                        "en" => $enData->name_simple,
                        "id" => $idData->name_simple,
                    ], "translation" => [
                        "en" => $enData->translated_name->name,
                        "id" => $idData->translated_name->name,
                    ],
                ],
                "bismillah_pre" => $enData->bismillah_pre,
                "preBismillah" => $enData->bismillah_pre ? static::preBismillah() : [],
                "relevation" => $relevation,
                "tafsir" => [
                    "en" => "TBD",
                    "id" => "TBD",
                ],
            ];
        }, $dataEn, $dataId);

        file_put_contents(storage_path("app/meta.json"), json_encode($listOfSurahs), LOCK_EX);
    }

    public static function crawlSurah($surah)
    {
        $metaFile = JsonReader::readJsonFileFromAppStorage("meta.json");
        $surahItem = $metaFile[$surah - 1];

        $uf = new UrlFetcher(env('APP_CRAWL_BASEURL1') . "/surah/$surah/editions/quran-simple-enhanced,ar.alafasy,en.transliteration,en.sahih,id.indonesian");

        $surahResponse = json_decode($uf->process());
        $surahDetail = $surahResponse->data;

        $arabData = $surahDetail[0]->ayahs;
        $arabAudioData = $surahDetail[1]->ayahs;
        $enLatinData = $surahDetail[2]->ayahs;
        $enTranslateData = $surahDetail[3]->ayahs;
        $idTranslateData = $surahDetail[4]->ayahs;

        $verses = array_map(function ($arab, $audio, $latin, $eng, $id) {
            return [
                "number" => [
                    "inQuran" => $arab->number,
                    "inSurah" => $arab->numberInSurah,
                ],
                "meta" => [
                    "juz" => $arab->juz,
                    "page" =>  $arab->page,
                    "manzil" =>  $arab->manzil,
                    "ruku" =>  $arab->ruku,
                    "hizbQuarter" =>  $arab->hizbQuarter,
                    "sajda" => [
                        "recommended" => false,
                        "obligatory" => $arab->sajda
                    ]
                ],
                "text" => [
                    "arab" => $arab->text,
                    "transliteration" => [
                        "en" => $latin->text
                    ]
                ],
                "translation" => [
                    "en" => $eng->text,
                    "id" => $id->text,
                ],
                "audio" => [
                    "primary" => $audio->audio,
                    "secondary" => $audio->audioSecondary
                ],
                "tafsir" => [
                    "id" => [
                        "short" => "TBD",
                        "long" => "TBD"
                    ]
                ]
            ];
        }, $arabData, $arabAudioData, $enLatinData, $enTranslateData, $idTranslateData);

        $surahItem->verses = $verses;

        file_put_contents(storage_path("app/surah.$surah.json"), json_encode($surahItem), LOCK_EX);
    }

    public static function preBismillah()
    {
        return  [
            "preBismillah" => [
                "text" => [
                    "arab" => "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ",
                    "transliteration" => ["en" => "Bismillaahir Rahmaanir Raheem"],
                ],
                "translation" => [
                    "en" =>
                    "In the name of Allah, the Entirely Merciful, the Especially Merciful.",
                    "id" => "Dengan nama Allah Yang Maha Pengasih, Maha Penyayang.",
                ],
                "audio" => [
                    "primary" =>
                    "https://cdn.alquran.cloud/media/audio/ayah/ar.alafasy/1",
                    "secondary" => [
                        "https://cdn.islamic.network/quran/audio/128/ar.alafasy/1.mp3",
                        "https://cdn.islamic.network/quran/audio/64/ar.alafasy/1.mp3",
                    ],
                ],
            ],
        ];
    }

    public static function loadSurah($surah)
    {
        $filePath = storage_path("app/surah.{$surah}.json");

        if (!file_exists($filePath)) throw new FileNotFoundException("Surat yang Anda cari tidak ditemukan.");

        $data = Items::fromFile($filePath);

        return iterator_to_array($data);
    }

    public static function loadSurahBig($surah)
    {
        $filePath = storage_path("app/quran.json");

        if (!file_exists($filePath)) throw new FileNotFoundException("Surat yang Anda cari tidak ditemukan.");

        $data = Items::fromFile($filePath);

        return iterator_to_array($data)[$surah - 1];
    }

    public static function combine()
    {
        $data = array();
        foreach (range(1, 85) as $n) {
            $data[] = static::loadSurah($n);
        }

        file_put_contents(storage_path("app/quran.json"), json_encode($data), LOCK_EX);
    }
}
