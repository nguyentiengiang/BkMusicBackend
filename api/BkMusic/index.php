<?php

/**
 * Define debug mode for apis application
 * set FALSE to turn off debug
 */
//define("DEBUG_MODE", FALSE);
define("DEBUG_MODE", TRUE);

/**
 * REQUIRE for run APIs
 * DO NOT Touch this file if must needed
 */
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
require(ROOT . 'config' . DIRECTORY_SEPARATOR . 'bootstrapSlimApi.php');

/**
 * 
 * Define name of api application choose what ever you want
 */
$classApplication = 'bkMusic';
require_once($classApplication . '.php');

/**
 * Function call to start Slim APIs application
 * 
 * @return void
 */

function startApp($classApplication) {
    new $classApplication();
}

/*
 * Run Slim app in mode defined
 */
if (!DEBUG_MODE) {
    startApp($classApplication);
} else {
    $bench = new Ubench;
    $bench->start();
    startApp($classApplication);
    $bench->end();
    $str = PHP_EOL . 'Time: ' . $bench->getTime(true) . ' microsecond -> ' . $bench->getTime(false, '%d%s');
    $str .= PHP_EOL . 'MemoryPeak: ' . $bench->getMemoryPeak(true) . ' bytes -> ' . $bench->getMemoryPeak(false, '%.3f%s');
    $str .= PHP_EOL . 'MemoryUsage: ' . $bench->getMemoryUsage(true);
    s10Core\MyFile\Log::write($str, $classApplication, "TestBenchmark" . $classApplication);
    unset($str);
}
