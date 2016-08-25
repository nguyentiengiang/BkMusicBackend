<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace s10;

define('ROOT', __DIR__);
require(ROOT. '/vendor/autoload.php');

/**
 * Define abstract s10App
 * @author TienGiang
 */
 
abstract class defaultApp {
    
    public function App();
    
}
