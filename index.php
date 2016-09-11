<?php

define('ROOT', __DIR__);
require(ROOT. '/vendor/autoload.php');

$app = new Slim\App();

$app->get('/hello/{name}', function ($request, $response, $args) {
    $data = array('name' => 'Rob', 'age' => 40);
    $newResponse = $response->withJson($data, 201);
    return $newResponse;
});

$app->run();