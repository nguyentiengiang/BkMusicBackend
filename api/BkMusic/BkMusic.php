<?php

namespace s10App;

use s10Core\DefaultApi;
use s10Core\ParserData;
use Valitron\Validator as V;

/**
 * BkMusic Application
 */

class BkMusic extends DefaultApi {
    
    /**
     * Re-define public properties in __construct() method (except $logMessage for WRITE TO LOG)
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
            'driver_options' => [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'],
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
        $this->app->get('/GetSongs', self::$appName . '::parserSongs')->setName('Songs');
        $this->app->get('/GetSinger', self::$appName . '::parserSinger')->setName('Singer');
    }
    
    public static function getCategories($request, $response, $args) {
        ORM::configure(self::$arrDatabaseConfigIdiOrm);
        $categories = ORM::for_table('Categories')
                ->select(['id', 'name', 'img', 'parent_id'])
                ->where_not_equal(['parent_id' => 0])
                ->find_array();
        return $response->withJson($categories, 200, JSON_OPTIONS);
    }
    
    public static function parserNhacHot($request, $response, $args) {
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
                'songName' => trim($liElement->find('div[class=item_content] a[class=name_song]', 0)->plaintext),
                'songUrl' => trim($liElement->find('div[class=item_content] a[class=name_song]', 0)->href),
                'singer' => trim($liElement->find('div[class=item_content] a[class=name_singer]', 0)->plaintext),
                'singerUrl' => trim($liElement->find('div[class=item_content] a[class=name_singer]', 0)->href),
            ]);
        }
        unset($liElements);
        $html->clear();
        unset($html);
        $appResponse = $response->withJson($arrSongs);
        return $appResponse;
    }

    public static function parserAlbum($request, $response, $args) {
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
        unset($liElements); $html->clear(); unset($html);
        $appResponse = $response->withJson($arrAlbums);
        return $appResponse;
    }
    
    public static function parserChart($request, $response, $args) {
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
        unset($liElements);
        $html->clear();
        unset($html);
        $appResponse = $response->withJson($arrChart);
        return $appResponse;
    }
    
    public static function parserSongs($request, $response, $args) {
        $params = $request->getQueryParams('urlSong', null);
        // validate URL
        $v = new V($params);
        $v->rule('required', ['urlSong']);
        $v->rule('url', ['urlSong']);
        
        if(!$v->validate()){
            return $response->withJson(ApiConstant::$JSON_ERROR_STATIC + ['message' => $v->errors()], 200);
        }
        $arrListTrack = self::extractTracklist($params['urlSong']);
        $appResponse = $response->withJson($arrListTrack);
        return $appResponse;
    }
    
    public static function parserSinger($request, $response, $args) {
        $params = $request->getQueryParams('urlSinger', null);
        // validate URL
        $v = new V($params);
        $v->rule('required', ['urlSinger']);
        $v->rule('url', ['urlSinger']);
        
        if(!$v->validate()){
            return $response->withJson(ApiConstant::$JSON_ERROR_STATIC + ['message' => $v->errors()], 200);
        }
        $html = ParserData::getHmltBySimpleDomParse($params['urlSinger']);
        
        $arrAlbums = [];
        $liAlbums = $html->find('div[class=list_album] div[class=fram_select] ul li');
        foreach ($liAlbums as $itemAlbum) {
            $albumArt = trim(array_shift($itemAlbum->find('div[class=box-left-album] span[class=avatar] img'))->getAttribute('data-src'));
            $albumName = trim(array_shift($itemAlbum->find('div[class=info_album] a[class=name_song]'))->plaintext);
            $albumUrl = trim(array_shift($itemAlbum->find('div[class=info_album] a[class=name_song]'))->href);
            array_push($arrAlbums, [
                'albumArt' => $albumArt,
                'albumName' => $albumName,
                'albumUrl' => $albumUrl,
            ]);
        }
        
        $arrSongs = [];
        $liSongs = $html->find('ul[class=list_item_music] li');
        foreach ($liSongs as $itemSong) {
            $songName = trim(array_shift($itemSong->find('div[class=item_content] a[class=name_song]'))->plaintext);
            $songUrl = trim(array_shift($itemSong->find('div[class=item_content] a[class=name_song]'))->href);
            array_push($arrSongs, [
                'songName' => $songName,
                'songUrl' => $songUrl
            ]);
        }
        
        $arrInfoSinger = [
            'albums' => $arrAlbums,
            'songs' => $arrSongs,
        ];
        
        $appResponse = $response->withJson($arrInfoSinger);
        return $appResponse;
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
    
    private static function extractTracklist($url) {
        // Get javascript content
        $html = ParserData::getHmltBySimpleDomParse($url);
        $srciptString = $html->find('div[class=playing_absolute] script[!src]');
        $scriptContent = array_shift($srciptString)->innertext;
        $arrScriptContent = explode('player.peConfig.xmlURL = "', $scriptContent);
        $arrSplited = explode('";', $arrScriptContent[1]);
        // cut string get flash xml file
        $urlPlaylist = $arrSplited[0];
        // unset html for Memory leak!
        $html->clear(); unset($html); unset($arrScriptContent); unset($arrSplited);
        //Get xml url
        $htmlTracks = ParserData::getHmltBySimpleDomParse($urlPlaylist);
        $tracks = $htmlTracks->find('tracklist track');
        $arrListTrack = [];
        foreach ($tracks as $track) {
            $title = $track->find('title');
            $creator = $track->find('creator');
            $newtab = $track->find('newtab');
            $bgimage = $track->find('bgimage');
            $avatar = $track->find('avatar');
            $key = $track->find('key');
            $location = $track->find('location');
            $info = $track->find('info');
            array_push($arrListTrack, [
                'songName' => self::extractStringInfoPlaylist(array_shift($title)->plaintext),
                'singer' => self::extractStringInfoPlaylist(array_shift($creator)->plaintext),
                'singerUrl' => self::extractStringInfoPlaylist(array_shift($newtab)->plaintext),
                'bgimage' => self::extractStringInfoPlaylist(array_shift($bgimage)->plaintext),
                'avatar' => self::extractStringInfoPlaylist(array_shift($avatar)->plaintext),
                'keyMp3' => self::extractStringInfoPlaylist(array_shift($key)->plaintext),
                'mp3Url' => self::extractStringInfoPlaylist(array_shift($location)->plaintext),
                'songUrl' => self::extractStringInfoPlaylist(array_shift($info)->plaintext),
            ]);
        }
        unset($tracks); $htmlTracks->clear(); unset($htmlTracks);
        return $arrListTrack;
    }

}