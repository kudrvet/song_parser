<?php

namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class ArtistRepository
{
    private static function init()
    {
        if (!Capsule::schema()->hasTable('media_artists')) {
            Capsule::schema()->create('media_artists', function (Blueprint $table) {
                $table->increments('artist_id');
                $table->string('username')->unique();
                $table->string('first_name');
                $table->string('full_name');
                $table->integer('followers_count');
                $table->timestamps();
            });
        }
    }

    /**
     * @param $data array array with Artist data like  [['field1name' => value1], ['field2name' => value2],...]
     * @return integer|null artistId or null
     */
    public static function saveAndGetId($data)
    {
       self::init();
       Capsule::table('media_artists')->upsert($data, ['username'], ['followers_count', 'first_name', 'full_name','created_at','updated_at']);
       return Capsule::table('media_artists')->select('artist_id')->where(['username' => $data['username']])->first()->artist_id;
    }
}
