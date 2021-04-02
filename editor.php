<?php
/**
 * Created by PhpStorm.
 * User: cfelix
 * Date: 17.07.2015
 * Time: 10:24
 */
require 'Slim/Slim.php';
$app = new \Slim\Slim();
$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});
$app->run();

