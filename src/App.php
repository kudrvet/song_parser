<?php

namespace App;
use Illuminate\Database\Capsule\Manager as Capsule;

class App
{
    const CONFIG_PATH = __DIR__ . '/../db.config.php';

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

    public static function loadArtistAndSongsInfo($profileUrl)
    {
        $fullSongsData = SongParser::getSongs($profileUrl);
        if (empty($fullSongsData)) {
            echo 'Artist have no songs';
            return;
        }

        $fullArtistData = $fullSongsData[0]['user'];
        $artistData = collect($fullArtistData)->only(['username', 'first_name', 'full_name', 'followers_count'])->all();
        $artistId = ArtistRepository::saveAndGetId($artistData);


        $songsData = collect($fullSongsData)
            ->map(fn($songData) => collect($songData)->only(['reposts_count', 'title', 'track_format', 'genre', 'duration'])
                ->union(['artist_id' => $artistId])
                ->all()
            )
            ->all();

        SongsRepository::saveSongs($songsData, $artistId);
    }

}