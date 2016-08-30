<?php

/**
 * BkMusic Application
 */

class BkMusic extends s10Core\defaultApp {
    
    /**
     * Re-define public properties in __construct() method (except $logMessage for WRITE TO LOG)
     * 
     */
    public function __construct() {
        self::$isWriteLog = TRUE;
        self::$isDebug = DEBUG_MODE;
        self::$appName = get_class($this);
        self::$arrDatabaseConfig = [];
        self::$arrSlimContainer = [];
        
        // Write magic method __construct() of parent(defaultApp) after re-define public properties
        parent::__construct();
    }
    
    /**
     * define request methods here
     * 
     */
    public function enableMethods() {
        $this->app->get('/{name}', self::$appName . '::index');
        $this->app->get('/json/GetCategories', self::$appName . '::GetCategories');
    }
        
    public function index($request, $response) {
        $name = $request->getAttribute('name');
        $response->getBody()->write("Hello, $name");
        return $response;
    }
    
    public function GetCategories($request, $response) {
        $response->getBody()->write("Hello");
        return $response;
    }
    
}