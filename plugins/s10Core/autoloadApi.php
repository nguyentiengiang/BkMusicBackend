<?php

/**
 * @todo set autoloading for s10Core
 * @version 1.2
 * 
 * @author Tien Giang <nguyentiengiang@outlook.com> Phone: +84 1282 303 100
 * @copyright (c) 2016, Tien Giang
 */

include_once 'DefaultApi.php';
include_once 'ParserData.php';
include_once 'MyFile/load.php';

if(!DEBUG_MODE){
    if (class_exists('Kint')) {
        Kint::enabled(FALSE);
    }
}

