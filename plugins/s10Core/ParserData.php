<?php

namespace s10Core;

/**
 * Description of parserData
 *
 * @author TienGiang
 */
class ParserData {
    
    /**
     * 
     * @param type $url
     * @return type Description
     */
    public static function getByCURL($url) {
        
    }
    
    /**
     * 
     * @param type $url
     * @return type Description
     */
    public static function getHmltBySimpleDomParse($url, $postData = null) {
        try {
            $context = null;
            if(!is_null($postData)){
                $postString = !is_null($postData) ? http_build_query($postData) : '';
                $opts = [
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $postString
                    ]
                ];
                $context = stream_context_create($opts);
            }
            return file_get_html($url, false, $context);
            unset($postData);
            unset($postString);
            unset($context);
        } catch (Exception $exc) {
        }
    }
    
}
