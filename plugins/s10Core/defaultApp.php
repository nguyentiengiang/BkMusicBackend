<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

namespace s10Core;

/**
 * @todo Define class Default Template Slim Application for s10API
 * @version 1.2
 * 
 * @author Tien Giang <nguyentiengiang@outlook.com> Phone: +84 1282 303 100
 * @copyright (c) 2016, Tien Giang
 */
 
class defaultApp {
    
    /**
     * public properties
     */
    public static $isWriteLog = TRUE;   // Set write log enable
    public static $logMessage = 'Running OK.';     // Use for write log
    
    public static $isDebug = TRUE;      // Set Debug Turn ON
    public static $appName = 'API';     // Set App Name
    // Set Database Configuration
    public static $arrDatabaseConfig = [
        'host' => 'localhost',
        'dbName' => 's10',
        'username' => 'root',
        'password' => '123456'
        ];
    // Set Slim Application Configuration
    public static $arrSlimContainer = [];
    
    /**
     * @todo For override __construct class App
     * @tutorial Re-define all 
     * 
     */
    public function __construct() {
        $this->app = new \Slim\App(self::$arrSlimContainer);
        $this->enableMethods();
        $this->generateRouterList();
        $this->app->run();
    }
    
    /**
     * @todo set Slim method get, map, ... 
     */
    public function enableMethods() {}
    
    /**
     * @todo Generation Router List into root(index) page.
     */
    private function generateRouterList(){
        foreach ($this->app->getContainer()->get('router')->getRoutes() as $route){
            if($route->getPattern() === '/'){
                echo("ERROR: Route '/' are really taken. Please choose another route!");die;
            }
        }
        $this->app->get('/', function ($request, $response) {
            $routers = array_slice($this->get('router')->getRoutes(), 0, -1);
            ddd($routers);
        });
    }

    /**
     * Write to log file (find in folder .s10AppsLog)
     */
    public function __destruct() {
        if (self::$isWriteLog) {
            MyFile\Log::write(self::$logMessage, self::$appName, 'LogApi');
        }
    }
        
    private function customSlimContainer($param) {
        $c['notFoundHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                return $c['response']
                    ->withStatus(404)
                    ->withHeader('Content-Type', 'text/html')
                    ->write('Page not found');
            };
        };
        return $arrSlimContainer;
    }
    
}
