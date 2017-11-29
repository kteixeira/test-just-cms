<?php

$router = new \Bramus\Router\Router();

$router->match('POST', '/auth', function(){
    $data = json_decode(file_get_contents('php://input'), true);
    \TestJustCms\JWTWrapper::getToken($data);
});

$router->mount('/posts', function() use ($router)
{
    $router->match('POST', '/create', function()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['post'])){
            header('HTTP/1.1 500 Internal Server Error', true, 500);
            die;
        }

        $PostController = new \TestJustCms\Controllers\PostController();
        $PostController->create($data['post']);
    });

    $router->put('/update/(\d+)', function($id){
        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['post'])){
            header('HTTP/1.1 500 Internal Server Error', true, 500);
            die;
        }

        $PostController = new \TestJustCms\Controllers\PostController();
        $PostController->update($id, $data['post']);
    });

    $router->get('/list', '\TestJustCms\Controllers\PostController@findAll');

    $router->get('/find/(\d+)', '\TestJustCms\Controllers\PostController@find');

    $router->delete('/delete/(\d+)', '\TestJustCms\Controllers\PostController@delete');
});

$router->before('GET|POST|PUT|DELETE', '/posts/.*', function()
{
    $headers = getallheaders();
    \TestJustCms\JWTWrapper::validateToken($headers);
});

$router->set404(function() {
    header('HTTP/1.1 404 Not Found', true, 404);
});

$router->run();