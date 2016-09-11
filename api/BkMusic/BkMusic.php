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
//        $this->app->get('/GetAlbum[/{id}[/{page}]]', self::$appName . '::parserAlbum')->setName('Album');
//        $this->app->get('/GetPlaylist/{url}', self::$appName . '::parserPlaylist')->setName('Playlist');
//        $this->app->get('/GetSongPlaylist/{url}', self::$appName . '::parserPlaylist')->setName('SongPlaylist');
//        $this->app->get('/GetPlaylist/{url}', self::$appName . '::parserPlaylist')->setName('MusicChart');        
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
//        $url = 'http://www.nhaccuatui.com/playlist/trai-tim-em-cung-biet-dau-va.sndGtwrsyqVZ.html';
//        ddd(base64_encode($url));
        $html = file_get_html('xml.htm');
        $tracks = $html->find('tracklist track');
        $arrList = [];
        foreach ($tracks as $track) {
            array_push($arrList, [
                'songName' => self::extractStringInfoPlaylist(array_shift($track->find('title'))->plaintext),
                'singer' => self::extractStringInfoPlaylist(array_shift($track->find('creator'))->plaintext),
                'bgimage' => self::extractStringInfoPlaylist(array_shift($track->find('bgimage'))->plaintext),
                'avatar' => self::extractStringInfoPlaylist(array_shift($track->find('avatar'))->plaintext),
                'keyMp3' => self::extractStringInfoPlaylist(array_shift($track->find('key'))->plaintext),
                'mp3Url' => self::extractStringInfoPlaylist(array_shift($track->find('location'))->plaintext),
                'songUrl' => self::extractStringInfoPlaylist(array_shift($track->find('info'))->plaintext),
            ]);
        }
//        $arrElementsHtml = $html->find('div[class=box_cata_control] ul[class=detail_menu_browsing_dashboard] li');
//        $arrList = [];
//        foreach ($arrElementsHtml as $eHtml){
//            $text = $eHtml->plaintext;
//            $url = array_shift($eHtml->find('a'))->href;
//            array_push($arrList, [
//                'text' => $text,
//                'url' => $url
//            ]);
//        }
        ddd($arrList);        
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