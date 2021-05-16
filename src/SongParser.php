<?php
namespace App;
use GuzzleHttp;

class SongParser
{
    const BASE_SONGS_URL = 'https://api-v2.soundcloud.com/users/';

    private static $headers = [
        'Accept' => 'application/json, text/javascript, */*; q=0.01',
        'Accept-Encoding' => 'gzip, deflate, br',
        'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
        'Host' => 'api-v2.soundcloud.com',
        'Origin' => 'https://soundcloud.com',
        'Pragma' => 'no-cache',
        'Referer' => 'https://soundcloud.com/',
        'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="90", "Google Chrome";v="90"',
        'sec-ch-ua-mobile' => '?0',
        'Sec-Fetch-Dest' => 'empty',
        'Sec-Fetch-Mode' => 'cors',
        'Sec-Fetch-Site' => 'same-site',
        'User-Agent'=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36'
    ];

    private static $defaultParams = [
        'representation' => '',
        'offset'         => '0',
        'limit'          => '20',
        'client_id'      => '7QSdC7H3pwqDSC2QTYEj4zqhSDz8SADR',
        'app_version'    => '1620646767',
        'app_locale'     => 'en',
        'linked_partitioning' => '1'
    ];

    private static $params = [];

    private static $httpClient;

    private static function init($params, $mergeParams)
    {
        self::$httpClient = new GuzzleHttp\Client();

        self::$params = $mergeParams ? array_merge(self::$defaultParams, $params) : $params;
    }

    public static function getSongs($profileUrl, $params = [] , $mergeParams = true)
    {
        self::init($params,$mergeParams);
        var_dump(self::$params);

        $artistId = self::getArtistId($profileUrl);

        $url = self::BASE_SONGS_URL . "{$artistId}/tracks";

        $response = self::$httpClient->request('GET', $url, ['headers' => self::$headers, 'query' => self::$params])->getBody();;

        $data = json_decode($response, true);

        $firstPartSongsData = $data['collection'];
        $otherPartsSongsData = self::getMoreSongsData($data);
        return array_merge($firstPartSongsData, $otherPartsSongsData);
    }

    public static function getArtistId($profileUrl)
    {
        try {
            $res =  self::$httpClient->request('GET', $profileUrl);
        }
        catch (\Exception $e) {
            echo "failure to get data from {$profileUrl}";
        }

        $response = (string)$res->getBody();

        if (preg_match("/soundcloud:\/\/users:(?P<digit>\d+)/", $response, $matches)){
            $id = $matches['digit'];
        } else {
            throw new \Exception("Artist id not found on {$profileUrl}");
        }

        return $id;
    }

    private static function getMoreSongsData($songData)
    {
        $result = [];
        $data = $songData;
        while (isset($data['next_href'])) {
            $nextPartUrl = $data['next_href'];
            $nextParamsQuery = parse_url($nextPartUrl, PHP_URL_QUERY);
            parse_str($nextParamsQuery,$nextParams);

            $nextResponse =   self::$httpClient->request('GET', $nextPartUrl,
                [
                    'headers' => self::$headers,
                    'query' => array_merge(self::$params, $nextParams)
                ]
            );

            $body = (string)$nextResponse->getBody();
            $nextData = json_decode($body, true);

            if (!empty($nextData['collection'])) {
                $result[] = $nextData['collection'];
            }

            $data = $nextData;
        }
        return collect($result)->flatten(1)->all();
    }
}
