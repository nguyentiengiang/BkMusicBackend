<?php

/**
 * Description of bootstrapSlimWeb
 *
 * @author TienGiang
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS . '..' . DS . '..' . DS);

require(ROOT . 'vendor' . DS . 'autoload.php');
require(ROOT . 'plugins' . DS . 's10Core' . DS .'autoloadWeb.php');