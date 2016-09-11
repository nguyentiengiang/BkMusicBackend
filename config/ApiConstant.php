<?php

/**
 * Global Constants for API Slim App
 * Store reused object, variable, array in app.
 * Array of constants, array of table lables and many other logic.
 * TODO can this way easy to implement multi-langual, use class instead of return array.
 * These general data can be use anywhere in application.
 */

/** ---- Define for single special type variable: DEFINDED string, numbers in PHP ---- **/
if (!defined('JSON_OPTIONS')){
    define('JSON_OPTIONS', JSON_NUMERIC_CHECK | JSON_HEX_TAG | JSON_HEX_APOS | 
            JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
}
/** ---- Define for list, array, hash, normal string, number ... ---- **/
class ApiConstant {
    public static $JSON_ERROR_NOT_FOUND = ['error' => true, 'message' => 'Item not found.'];
    
}