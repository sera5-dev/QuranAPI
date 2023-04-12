<?php

namespace App\Lib;

use Josantonius\Json\Json;
use JsonMachine\Items;

class QuranCrawler
{
    public static function fetchMetaDatas()
    {
        $url1 = "https://web-api.qurankemenag.net/quran-surah";
        $url2 = "https://api.quran.com/api/v4/chapters?language=en";
        $url3 = "http://api.alquran.cloud/v1/meta";
        $url4 = "https://equran.id/api/v2/surat";

        $uf1 = new UrlFetcher($url1);
        $uf2 = new UrlFetcher($url2);
        $uf3 = new UrlFetcher($url3);
        $uf4 = new UrlFetcher($url4);

        JsonLib::urlFetcherToJsonFile($uf1, "app/data/meta/kemenag.json");
        JsonLib::urlFetcherToJsonFile($uf2, "app/data/meta/quran.com.json");
        JsonLib::urlFetcherToJsonFile($uf3, "app/data/meta/alquran.cloud.json");
        JsonLib::urlFetcherToJsonFile($uf4, "app/data/meta/equran.json");
    }

    public static function newParseMetas()
    {
        $jsonOut = new Json(FileLib::createOrUnlink(storage_path("app/data/meta/combined.json")));

        $quranCom = QuranMetaParser::getMainMetaParser();

        $jsonOut->set();

        foreach ($quranCom as $key => $surah) {
            $kemenagMeta = QuranMetaParser::getKemenagMeta($surah->id);
            $eQuranMeta = QuranMetaParser::getEQuranMeta($surah->id);

            $jsonOut->push(
                [
                    "number" => $surah->id,
                    "sequence" => $surah->revelation_order,
                    "numberOfVerses" => $surah->verses_count,
                    "name" => [
                        "short" => $surah->name_arabic,
                        "long" => "سُورَةُ $surah->name_arabic",
                        "transliteration" => [
                            "en" => $surah->name_simple,
                            "id" => $kemenagMeta['transliteration']
                        ],
                        "translation" => [
                            "en" => $surah->translated_name->name,
                            "id" => $kemenagMeta['translation']
                        ]
                    ],
                    "revelation" => static::processRevelation($surah->revelation_place),
                    "preBismillah" => static::processPreBismillah($surah->bismillah_pre),
                    "tafsir" => [
                        "id" => strip_tags($eQuranMeta['deskripsi'])
                    ]
                ]
            );
        }

        return $jsonOut->get();
    }

    protected static function processRevelation($revelation_place)
    {
        if ($revelation_place == "makkah") {
            return ["arab" => "مكة", "en" => "Meccan", "id" => "Makkiyyah"];
        } else {
            return ["arab" => "مدينة", "en" => "Medinan", "id" => "Madaniyyah"];
        }
    }

    protected static function processPreBismillah($preBismillah)
    {
        return $preBismillah ?   [
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
        ] : [];
    }

    protected static function processSajda($surah, $verse)
    {
        $sajdaArray = QuranMetaParser::getQuranCloudMeta("/data/sajdas/references");

        foreach ($sajdaArray as $sajdaItem) {
            if ($sajdaItem->surah == $surah && $sajdaItem->ayah == $verse) {
                return [
                    "recommended" => $sajdaItem->recommended,
                    "obligatory" => $sajdaItem->obligatory,
                ];
            }
        }

        return [
            "recommended" => false,
            "obligatory" => false
        ];
    }

    public static function newCrawlSurah($surah)
    {
        $jsonOut = new Json(FileLib::createOrUnlink(storage_path("app/data/surah/{$surah}.json")));

        $surahDetail = QuranMetaParser::getCombinedMeta($surah - 1);

        $verses = [];


        $quranCloudData = JsonReader::readFromUrl("https://api.alquran.cloud/v1/surah/$surah/editions/quran-simple-enhanced,ar.alafasy,en.transliteration,en.sahih,id.indonesian", "/data");

        $eQuranData = JsonReader::readFromUrl("https://equran.id/api/v2/surat/$surah", "/data/ayat");

        $arabAyatLists = $quranCloudData[0]->ayahs;
        $arabAudioAyatLists =  $quranCloudData[1]->ayahs;
        $englishTransliterationAyatLists = $quranCloudData[2]->ayahs;
        $englishAyatLists = $quranCloudData[3]->ayahs;

        foreach ($arabAyatLists as $ayatKey => $ayatData) {
            $eqAyat = $eQuranData[$ayatKey];
            $enAyat = $englishAyatLists[$ayatKey];
            $tenAyat = $englishTransliterationAyatLists[$ayatKey];
            $audioAyat = $arabAudioAyatLists[$ayatKey];

            $particularVerse = [
                "number" => [
                    "inQuran" => $ayatData->number,
                    "inSurah" => $ayatData->numberInSurah,
                ],
                "meta" => [
                    "juz" => $ayatData->juz,
                    "surah" => $surahDetail['number'],
                    "page" => $ayatData->page,
                    "manzil" => $ayatData->ruku,
                    "hizbQuarter" => $ayatData->hizbQuarter,
                    "sajda" => static::processSajda($surahDetail['number'], $ayatData->numberInSurah)
                ],
                "text" => [
                    "arab" => $eqAyat->teksArab,
                    "transliteration" => [
                        "en" => $tenAyat->text,
                        "id" => $eqAyat->teksLatin
                    ]
                ],
                "translation" => [
                    "en" => $enAyat->text,
                    "id" => $eqAyat->teksIndonesia
                ],
                "audio" => [
                    "primary" => $audioAyat->audio,
                    "secondary" => $audioAyat->audioSecondary
                ],
                "tafsir" => [
                    "id" => [
                        "long" => "-",
                        "short" => "-",
                    ]
                ]
            ];

            static::appendVerseToPage($particularVerse['meta']['page'], $particularVerse);
            static::appendVerseToJuz($particularVerse['meta']['juz'], $particularVerse);

            $verses = [
                ...$verses,
                $particularVerse
            ];
        }

        $surahDetail['verses'] = $verses;

        $jsonOut->set($surahDetail);

        return $surahDetail;
    }



    //=======================================
    public static function readMetaEntryPoint()
    {

        $lang = ["id", "en"];

        static::fetchSurahsFromQuranCom($lang);
        static::parseMeta();
    }

    public static function fetchSurahsFromQuranCom($languages = ['id', 'en'])
    {
        foreach ($languages as $langItem) {
            $uf = new UrlFetcher("https://api.quran.com/api/v4/chapters");

            $uf->buildGetQuery([
                "language" => $langItem
            ]);

            $res = $uf->process();

            $response = json_decode($res);

            $data = $response->chapters;

            file_put_contents(storage_path("app/meta_qurancom.$langItem.json"), json_encode($data), LOCK_EX);
        }
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

    public static function appendVerseToPage($page, $verse)
    {
        $pageFilePath = storage_path("app/data/pages/$page.json");

        if (!file_exists($pageFilePath)) {
            touch($pageFilePath);
        }

        $fileContent = json_decode(file_get_contents($pageFilePath));
        if (empty($fileContent)) $fileContent = [];

        $fileContent[] = $verse;

        file_put_contents($pageFilePath, json_encode($fileContent), LOCK_EX);
    }

    public static function appendVerseToJuz($juz, $verse)
    {
        $filePath = storage_path("app/data/juz/$juz.json");

        if (!file_exists($filePath)) {
            touch($filePath);
        }

        $fileContent = json_decode(file_get_contents($filePath));
        if (empty($fileContent)) $fileContent = [];

        $fileContent[] = $verse;

        file_put_contents($filePath, json_encode($fileContent), LOCK_EX);
    }

    public static function crawlSurah($surah)
    {
        $metaFile = JsonReader::readJsonFileFromAppStorage("meta.json");
        $surahItem = $metaFile[$surah - 1];

        $uf = new UrlFetcher("https://api.alquran.cloud/v1/surah/$surah/editions/quran-simple-enhanced,ar.alafasy,en.transliteration,en.sahih,id.indonesian");
        $kemenagUf = new UrlFetcher("https://web-api.qurankemenag.net/quran-ayah");

        $kemenagUf->buildGetQuery([
            "surah" => $surah
        ]);

        $surahResponse = json_decode($uf->process());
        $kemenagResponse = json_decode($kemenagUf->process());

        $surahDetail = $surahResponse->data;
        $kemenagDetail = $kemenagResponse->data;

        $arabData = $surahDetail[0]->ayahs;
        $arabAudioData = $surahDetail[1]->ayahs;
        $enLatinData = $surahDetail[2]->ayahs;
        $enTranslateData = $surahDetail[3]->ayahs;
        $idTranslateData = $surahDetail[4]->ayahs;

        $verses = array_map(
            function ($arab, $audio, $latin, $eng, $id, $kemenag) use ($surahItem) {
                //$uf = new UrlFetcher("https://web-api.qurankemenag.net/quran-tafsir/{$arab->number}");
                //$res = json_decode($uf->process())->data;
                //sleep(rand(2, 5));

                $verseResult = [
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
                            "recommended" => $arab->sajda,
                            "obligatory" => $arab->sajda
                        ],
                    ],
                    "surah" => array_diff_key((array) $surahItem, array_flip(['verses'])),
                    "text" => [
                        "arab" =>  $kemenag->arabic,
                        "transliteration" => [
                            "en" => $latin->text,
                            "id" =>  $kemenag->latin,
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
                            "short" => "-",
                            "long" => "-"
                        ]
                    ]
                ];

                static::appendVerseToPage($arab->page, $verseResult);
                static::appendVerseToJuz($arab->juz, $verseResult);

                return $verseResult;
            },
            $arabData,
            $arabAudioData,
            $enLatinData,
            $enTranslateData,
            $idTranslateData,
            $kemenagDetail
        );

        $surahItem->verses = $verses;

        file_put_contents(storage_path("app/quran-data/surahs/surah.$surah.json"), json_encode($surahItem), LOCK_EX);
    }
}
