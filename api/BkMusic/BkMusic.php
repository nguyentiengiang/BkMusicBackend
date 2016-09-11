<?php

use s10Core\DefaultApi;
use s10Core\ParserData;

/**
 * BkMusic Application
 */

class BkMusic extends DefaultApi {
    
    /**
     * Re-define public properties in __construct() method (except $logMessage for WRITE TO LOG)
     * 
     */
    public function __construct() {
        self::$isWriteLog = TRUE;
        self::$isDebug = DEBUG_MODE;
        self::$appName = get_class($this);
        self::$arrDatabaseConfigIdiOrm = [
            'connection_string' => 'mysql:host=localhost;dbname=BkMusic',
            'username' => 'root',
            'password' => '',
            'return_result_sets' => true,
            'driver_options' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                ],
        ];
        self::$arrSlimContainer = [];
        
        // Write magic method __construct() of parent(defaultApp) after re-define public properties
        parent::__construct();
    }
    
    /**
     * define request methods here
     * 
     */
    public function enableMethods() {
        $this->app->get('/GetCategories', self::$appName . '::getCategories')->setName('Category');
        $this->app->get('/GetNhacHot[/{playlistId}[/{page}]]', self::$appName . '::parserNhacHot')->setName('NhacHot');
        $this->app->get('/GetAlbum[/{albumListId}[/{page}]]', self::$appName . '::parserAlbum')->setName('Album');
        $this->app->get('/GetChart[/{chartId}]', self::$appName . '::parserChart')->setName('MusicChart');
        $this->app->get('/GetSongPlaylist/{url}', self::$appName . '::parserSongPlaylist')->setName('SongPlaylist');
//        $this->app->get('/GetSong/{url}', self::$appName . '::parserSong')->setName('Song');
//        
    }
    
    public function getCategories($request, $response, $args) {
        ORM::configure(self::$arrDatabaseConfigIdiOrm);
        $categories = ORM::for_table('Categories')
                ->select(['id', 'name', 'img', 'parent_id', 'is_delete'])
                ->find_array();
        return $response->withJson($categories, 200, JSON_OPTIONS);
    }
    
    public function parserNhacHot($request, $response, $args) {
        $playlistId = isset($args['playlistId']) ? $args['playlistId'] : '1';
        $page = (isset($args['page']) && $args['page'] != 1) ? intval($args['page']) : '';
        ORM::configure(self::$arrDatabaseConfigIdiOrm);
        $categories = ORM::for_table('Categories')
                ->where(['is_delete' => 0, 'parent_id' => 1])
                ->find_one($playlistId);
        if (!$categories) {
            return $response->withJson(ApiConstant::$JSON_ERROR_NOT_FOUND, 404);
        }
        $url = $categories->url;
        $url .= ((is_numeric($page)) ? $page . '.' : '') . 'html';
        
        $arrSongs = [];
        $html = ParserData::getHmltBySimpleDomParse($url);
        $liElements = $html->find('ul[class=list_item_music] li');
        foreach ($liElements as $liElement) {
            $contentSong = $liElement->find('div[class=item_content]');
            array_push($arrSongs, [
                'songName' => trim(array_shift($contentSong)->plaintext),
                'songUrl' => trim($liElement->find('div[class=item_content] a[class=name_song]', 0)->href),
                'singer' => trim($liElement->find('div[class=item_content] a[class=name_singer]', 0)->plaintext),
                'singerUrl' => trim($liElement->find('div[class=item_content] a[class=name_singer]', 0)->href),
            ]);
        }
        $appResponse = $response->withJson($arrSongs);
        return $appResponse;
    }

    public function parserAlbum($request, $response, $args) {
        $albumId = isset($args['albumListId']) ? $args['albumListId'] : '29';
        $page = (isset($args['page']) && $args['page'] != 1) ? intval($args['page']) : '';
        ORM::configure(self::$arrDatabaseConfigIdiOrm);
        $categories = ORM::for_table('Categories')
                ->where(['is_delete' => 0, 'parent_id' => 2])
                ->find_one($albumId);
        if (!$categories) {
            return $response->withJson(ApiConstant::$JSON_ERROR_NOT_FOUND, 404);
        }
        $url = $categories->url;
        $url .= ((is_numeric($page)) ? $page . '.' : '') . 'html';
        
        $arrAlbums = [];
        $html = ParserData::getHmltBySimpleDomParse($url);
        $liElements = $html->find('div[class=list_album]', 1)
                ->find('div[class=fram_select] ul li');
        foreach ($liElements as $liElement) {
            array_push($arrAlbums, [
                'albumArt' => trim(array_shift($liElement->find('div[class=box-left-album] span[class=avatar] img'))->getAttribute('data-src')),
                'albumName' => trim(array_shift($liElement->find('div[class=info_album] a[class=name_song]'))->plaintext),
                'albumUrl' => trim(array_shift($liElement->find('div[class=info_album] a[class=name_song]'))->href),
                'singer' => trim(array_shift($liElement->find('div[class=info_album] a[class=name_singer]'))->plaintext),
                'singerUrl' => trim(array_shift($liElement->find('div[class=info_album] a[class=name_singer]'))->href),
            ]);
        }
        $appResponse = $response->withJson($arrAlbums);
        return $appResponse;
    }
    
    public function parserChart($request, $response, $args) {
        $albumId = isset($args['chartId']) ? $args['chartId'] : '45';
        
        ORM::configure(self::$arrDatabaseConfigIdiOrm);
        $categories = ORM::for_table('Categories')
                ->where(['is_delete' => 0, 'parent_id' => 3])
                ->find_one($albumId);
        if (!$categories) {
            return $response->withJson(ApiConstant::$JSON_ERROR_NOT_FOUND, 404);
        }
        $url = $categories->url;
        $arrSongs = [];
        $html = ParserData::getHmltBySimpleDomParse($url);
        $playlistUrl = array_shift($html->find('div[class=box_view_week] a[class=active_play]'))->href;
        $liElements = $html->find('div[class=list_chart_page] ul[class=list_show_chart] li');
        foreach ($liElements as $liElement) {
            array_push($arrSongs, [
                'albumArt' => trim(array_shift($liElement->find('div[class=box_info_field] a img'))->getAttribute('src')),
                'songName' => trim(array_shift($liElement->find('div[class=box_info_field] a[class=name_song]'))->plaintext),
                'songUrl' => trim(array_shift($liElement->find('div[class=box_info_field] a[class=name_song]'))->href),
                'singer' => trim($liElement->find('div[class=box_info_field] a[class=name_singer]', 0)->plaintext),
                'singerUrl' => trim($liElement->find('div[class=box_info_field] a[class=name_singer]', 0)->href),
            ]);
        }
        $arrChart = [
            'playAllUrl' => $playlistUrl,
            'songs' => $arrSongs
        ];
        $appResponse = $response->withJson($arrChart);
        return $appResponse;
    }
    
    public function parserSongPlaylist($request, $response, $args) {
        // using base64_decode for url encoded
        $url = isset($args['url']) ? base64_decode($args['url']) : '';
        if(!isset($args['url'])){
            return $response->withJson(ApiConstant::$JSON_ERROR_NOT_FOUND, 404);
        }
        // Get javascript content
        $html = ParserData::getHmltBySimpleDomParse($url);
        $scriptContent = array_shift($html->find('div[class=playing_absolute] script[!src]'))->innertext;
        $arrScriptContent = explode('player.peConfig.xmlURL = "', $scriptContent);
        $arrSplited = explode('";', $arrScriptContent[1]);
        // cut string get flash xml file
        $urlPlaylist = $arrSplited[0];
        //Get xml url
        $htmlTracks = ParserData::getHmltBySimpleDomParse($urlPlaylist);
        $tracks = $htmlTracks->find('tracklist track');
        $arrListTrack = [];
        foreach ($tracks as $track) {
            array_push($arrListTrack, [
                'songName' => self::extractStringInfoPlaylist(array_shift($track->find('title'))->plaintext),
                'singer' => self::extractStringInfoPlaylist(array_shift($track->find('creator'))->plaintext),
                'singerUrl' => self::extractStringInfoPlaylist(array_shift($track->find('newtab'))->plaintext),
                'bgimage' => self::extractStringInfoPlaylist(array_shift($track->find('bgimage'))->plaintext),
                'avatar' => self::extractStringInfoPlaylist(array_shift($track->find('avatar'))->plaintext),
                'keyMp3' => self::extractStringInfoPlaylist(array_shift($track->find('key'))->plaintext),
                'mp3Url' => self::extractStringInfoPlaylist(array_shift($track->find('location'))->plaintext),
                'songUrl' => self::extractStringInfoPlaylist(array_shift($track->find('info'))->plaintext),
            ]);
        }
        $appResponse = $response->withJson($arrListTrack);
        return $appResponse;
    }
    
    public function parserSong($request, $response, $args) {
        
    }
    //Search http://www.nhaccuatui.com/ajax/search?q=noo%20phu
    
    private static function extractStringInfoPlaylist($str) {
        $strExtract = trim($str);
        $arrReplace = [
            '<![CDATA[' => '',
            ']]>' => ''
        ];
        return str_replace(array_keys($arrReplace), array_values($arrReplace), $strExtract);
    }
    
}