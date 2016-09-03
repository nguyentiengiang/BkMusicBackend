<?php

/**
 * BkMusic Application
 */

class bkMusic extends s10Core\defaultApi {
    
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
        $this->app->get('/GetCategories', self::$appName . '::getCategories')->setName('Category');
    }
    
    public function getCategories($request, $response) {
        $response->getBody()->write("Hello");
        return $response;
    }
    
}