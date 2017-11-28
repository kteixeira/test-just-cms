<?php

$router = new \Bramus\Router\Router();

$router->get('/', '\TestJustCms\Controllers\PostController@findAll');

$router->run();