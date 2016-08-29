<?php

namespace s10Core;

/**
 * @todo Define class Default Template Slim Application for s10API
 * @version 1.2
 * 
 * @author Tien Giang <nguyentiengiang@outlook.com> Phone: +84 1282 303 100
 * @copyright (c) 2016, Tien Giang
 */
 
class defaultApp {
    
    //public properties
    public static $isWriteLog = TRUE;
    public static $isDebug = TRUE;
    public static $appName = 'API';
    public static $arrDatabaseConfig = [
        'host' => 'localhost',
        'dbName' => 's10',
        'username' => 'root',
        'password' => '123456'
        ];
    public static $arrSlimConfig = [
        ''
    ];
    public static $logMessage = '';


    public function __construct() {}
    
    /**
     * Write to log file (find in folder .s10AppsLog)
     */
    public function __destruct() {
        if (self::$isWriteLog) {
            MyFile\Log::write(self::$logMessage, self::$appName);
        }
    }
    
    public function enable(){}
    
}
