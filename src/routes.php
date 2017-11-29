<?php

$router = new \Bramus\Router\Router();

$router->mount('/posts', function() use ($router)
{
    $router->match('POST', '/', function()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['post'])){
            header('HTTP/1.1 500 Internal Server Error', true, 500);
            die;
        }

        $PostController = new \TestJustCms\Controllers\PostController();
        $PostController->create($data['post']);
    });

    $router->put('/(\d+)', function($id){
        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['post'])){
            header('HTTP/1.1 500 Internal Server Error', true, 500);
            die;
        }

        $PostController = new \TestJustCms\Controllers\PostController();
        $PostController->update($id, $data['post']);
    });

    $router->get('/', '\TestJustCms\Controllers\PostController@findAll');

    $router->get('/(\d+)', '\TestJustCms\Controllers\PostController@find');

    $router->delete('/(\d+)', '\TestJustCms\Controllers\PostController@delete');
});

$router->set404(function() {
    header('HTTP/1.1 404 Not Found', true, 404);
});

$router->run();