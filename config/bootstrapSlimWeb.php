<?php

/**
 * Description of bootstrapSlimWeb
 *
 * @author TienGiang <nguyentiengiang@outlook.com>
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', __DIR__ . DS . '..' . DS);

require_once ROOT_DIR . 'vendor' . DS . 'autoload.php';
require_once ROOT_DIR . 'plugins' . DS . 's10Core' . DS .'autoloadWeb.php';
