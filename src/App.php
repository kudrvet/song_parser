<?php

namespace App;
use Illuminate\Database\Capsule\Manager as Capsule;

class App
{
    const CONFIG_PATH = __DIR__ . '/../db.config.php';

    /** create connection to db
     * @throws \Exception
     */
    public static function createDbConnection()
    {
        if (file_exists(self::CONFIG_PATH)) {
               $config =  require self::CONFIG_PATH;
        } else {
            throw new \Exception('There is no db.config.php file in project root');
        }

        $capsule = new Capsule();
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
    }

    /**
     * @param $profileUrl string artist profile url on https://soundcloud.com/
     * @param $params array query params to make request with. If
     * @param $mergeParams int if true, that  if the $params array have the same string keys with default params array, then the value in this
     * array for that key will overwrite the default. If false, default params will be removed. The current params will be applied.
     * @throws \Exception
     */
    public static function loadArtistAndSongsInfo($profileUrl, $params = [], $mergeParams = true)
    {
        $fullSongsData = SongParser::getSongs($profileUrl, $params, $mergeParams);
        if (empty($fullSongsData)) {
            echo 'Artist have no songs. Load was interrupted';
            return;
        }

        $fullArtistData = $fullSongsData[0]['user'] ?? null;
        if (is_null($fullArtistData)) {
            throw new \Exception('soundcloud api has been changed. This program does not work anymore :(');
        }
        $now = date('Y-m-d H:i:s');

        $artistData = collect($fullArtistData)
            ->only(['username', 'first_name', 'full_name', 'followers_count'])
            ->union(['created_at' => $now, 'updated_at' => $now])
            ->all();
        $artistId = ArtistRepository::saveAndGetId($artistData);

        $songsData = collect($fullSongsData)
            ->map(fn($songData) => collect($songData)
                ->only(['reposts_count', 'title', 'track_format', 'genre', 'duration'])
                ->union(['artist_id' => $artistId, 'created_at' => $now, 'updated_at' => $now])
                ->all()
            )
            ->all();

        SongsRepository::save($songsData);
    }
}
