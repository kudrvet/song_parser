# song_parser

### DESCRIPTION
Скрипт парсит данные об исполнителе и его библиотеке треков c сайта soundcloud.com в базу данных.
Есть возможность задать опциональные параметры запроса. 

## Установка

- клонируйте репозиторий
- composer install
- создайте файл db.config.php по аналогии с db.config.example.php и заполните настройки подключения к вашей бд

## Использование
- запутите скрипт по пути bin/loadArtistLibrary
- скрипт поддерживает --help
- после успешного выполнения в вашей бд создадуться таблитцы media_artists и media_tracks c данными об исполнителях и треках соответственно 
