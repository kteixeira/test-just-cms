<?php

$router = new \Bramus\Router\Router();

//Rota de autenticação
$router->match('POST', '/auth', function(){
    $data = json_decode(file_get_contents('php://input'), true);
    \TestJustCms\JWTWrapper::getToken($data);
});

//Grupo de rotas para os métodos de Posts
$router->mount('/posts', function() use ($router)
{
    //Rota responsável pela criaçãod de posts
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

    //Rota responsável pela edição de posts
    $router->put('/(\d+)', function($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['post'])){
            header('HTTP/1.1 500 Internal Server Error', true, 500);
            die;
        }

        $PostController = new \TestJustCms\Controllers\PostController();
        $PostController->update($id, $data['post']);
    });

    //Rota que fará a busca de todos os posts
    $router->get('/', '\TestJustCms\Controllers\PostController@findAll');

    //Rota que fará a busca de um post específico
    $router->get('/(\d+)', '\TestJustCms\Controllers\PostController@find');

    //Rota que fará a busca de um post específico
    $router->delete('/(\d+)', '\TestJustCms\Controllers\PostController@delete');
});

//Middleware responsável por fazer a validação do Token
$router->before('GET|POST|PUT|DELETE', '/posts.*', function()
{
    $headers = getallheaders();
    \TestJustCms\JWTWrapper::validateToken($headers);
});

$router->set404(function() {
    header('HTTP/1.1 404 Not Found', true, 404);
});

$router->run();