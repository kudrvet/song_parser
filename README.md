# song_parser

### DESCRIPTION
Скрипт парсит данные об исполнителе и его библиотеке треков c сайта soundcloud.com в базу данных.
Есть возможность задать опциональные параметры запроса. 

## Установка

- клонируйте репозиторий
- composer install
- создайте файл db.config.php по аналогии с db.config.example.php и заполните настройки подключения к вашей бд
Пример :
```php
<?php

return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'parser',
    'username' => 'root',
    'password' => '1234567890',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
];
```

## Использование
- запутите скрипт по пути bin/loadArtistLibrary
```bash
loadArtistLibrary https://soundcloud.com/dekobe
```
- скрипт поддерживает --help
```bash
Load artist information and songs library from soundcloud.com to database.

Usage:
  loadArtistLibrary (-h|--help)
  loadArtistLibrary (-v|--version)
  loadArtistLibrary (-d|--default)         
  loadArtistLibrary  [--params=<params>] [--overwrite] <profileUrl>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  -d --default                  Show default params
  --params=<params>             Query params to make requests with.(Example:'client_id=12345,app_locale=ru,...').
                                This params will be merged with params by default.
                                If params have the same with default params, then the value for that params will overwrite params by default. 
                                Set --overwrite to remove default params and apply only current params. 
                                If not set, default params will be apply. [default: ].
  --overwrite                   If set, default params will be removed. The current params will be applied.
  <profileUrl>                  artist profile url on https://soundcloud.com/
  ```
- используйте флаг --params для изменения параметров запроса  по умолчанию. Добавленные параметры перезапишут дефолтные в случае совпадения.
```bash
loadArtistLibrary --params client_id=akn1232j3n23,offset=50 https://soundcloud.com/dekobe
```
- добавьте флаг --overwrite чтобы полностью стереть дефолтные настройки и установить текущие
```bash
loadArtistLibrary --params client_id=akn1232j3n23,offset=50 --create  https://soundcloud.com/dekobe
```
- после успешного выполнения в вашей бд создадуться таблитцы media_artists и media_tracks c данными об исполнителях и треках соответственно 
