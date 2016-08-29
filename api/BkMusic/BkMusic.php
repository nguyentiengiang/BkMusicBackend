<?php

/**
 * 
 */

class BkMusic extends s10Core\defaultApp {

    public function __construct() {
        self::$isDebug = MODE_APP;
        self::$isWriteLog = TRUE;
        self::$appName = get_class($this);
        self::$arrDatabaseConfig = [];
        self::$arrSlimConfig = [];
                
    }


    public function App() {
        d(self::$appName);        
        self::$logMessage = 'OK';
    }
 
    
    
}