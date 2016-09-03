<?php

namespace s10Core\MyFile;

/**
 * @todo Write to Log file of s10 App Project
 * @version 1.2
 * 
 * @author Tien Giang <nguyentiengiang@outlook.com> Phone: +84 1282 303 100
 * @copyright (c) 2016, Tien Giang
 */
class Log {

    /**
     * @todo write log message
     * @param $message string
     */
    private static $path = null;
    private static $fileLog = null;
    private static $content = null;

    static function write($message = null, $app = 'API', $fnc = 'api') {
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/.s10AppsLog/' . $app)) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/.s10AppsLog/' . $app, 0755, true);
        }
        self::$path = $_SERVER['DOCUMENT_ROOT'] . '/.s10AppsLog/' . $app . '/';
        self::$fileLog = '[' . date('Y.m.d') . '] - ' . $app . '@' . $fnc . '.txt';
        self::$content = '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL . '--------------' . PHP_EOL;
        File::write(self::$path . '/' . self::$fileLog, self::$content, true);
    }

    function __destruct() {
        unset(self::$path);
        unset(self::$fileLog);
        unset(self::$content);
    }

}
