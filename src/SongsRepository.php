<?php

namespace App;

use GuzzleHttp;
use function PHPUnit\Framework\throwException;
use Illuminate\Database\Capsule\Manager as Capsule;


class SongsRepository
{
    private static function init()
    {
        if (!Capsule::schema()->hasTable('media_tracks')) {
            Capsule::schema()->create('media_tracks', function ($table) {
//                $table->increments('track_id');
                $table->string('artist_id');
                $table->string('title');
                $table->integer('reposts_count');
                $table->string('track_format');
                $table->string('genre');
                $table->integer('duration');
                $table->primary(['artist_id', 'title']);
                $table->timestamps();
            });
        }
    }

    public static function saveSongs($data, $artistId)
    {
        self::init();
        Capsule::table('media_tracks')->upsert($data, ['artist_id','title'], ['reposts_count']);


    }

}
