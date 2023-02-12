# Quran API
This Quran API is designed to be compatible (to a certain degree, of course) with Gading Nasution's Quran API (https://api.quran.gading.dev/). 

### Deployment
> This API is live now at: https://quran-api.ptalmaun.com/v1

### Features
- [x] Arabic Transliteration (English spelling)
- [x] English and Indonesia translation
- [x] Verses meta (juz, sajda, manzil, etc)
- [x] Audio (***Syekh. Mishary Rashid Al-Afasy*** murrotal edition)
- [ ] Tafsir (TBD)
- [ ] Your request(s) are welcome!

### Endpoint usage
- [x] `/surah` = Returns the list of surahs in Al-Quran.
- [x] `/surah/{surah}` = Returns specific surah. **Example: [/surah/110](https://quran-api.ptalmaun.com/v1/surah/110)**
- [x] `/surah/random` = Returns random surah. **Example: [/surah/random](https://quran-api.ptalmaun.com/v1/surah/random)**
- [x] `/surah/{surah}/{ayah}` = Returns spesific ayah with requested surah. **Example: [/surah/2/255](https://quran-api.ptalmaun.com/v1/surah/2/255)**
- [x] `/surah/random/{ayah}` = Returns specific ayah from a random surah. If the ayah does not exists, then it returns first ayah. **Example: [/surah/random/1](https://quran-api.ptalmaun.com/v1/surah/random/1)**
- [x] `/surah/{surah}/random` = Returns random ayah from specified surah. If the ayah does not exists, then it returns first ayah. **Example: [/surah/random/1](https://quran-api.ptalmaun.com/v1/surah/1/random)**
- [x] `/surah/random/random` = Returns a random ayah from a random surah **Example: [/surah/random/random](https://quran-api.ptalmaun.com/v1/surah/random/random)**

### Data Source
- [api.alquran.cloud](https://api.alquran.cloud): Surah details, arabic text, transliterations, and English/Indonesian translations.
- [api.quran.com/api/v4](https://api.quran.com/api/v4): Surah lists.

### Special Thanks

 1. Gading Nasution's Quran API (https://github.com/gadingnst/quran-api) 
 2. https://stackedit.io/ -- I edit README.md there.
