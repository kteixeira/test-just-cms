<?php

/**
 * Application Controller.
 *
 * @author     Kaio Teixeira
 */

namespace TestJustCms\Controllers;
use TestJustCms\Models\Posts;

class PostController
{
    /**
     * Método responsável pela criação de um novo post
     * @param $post
     */
    public function create($post)
    {
        if(!isset($post['title']) || is_null($post['title']))
            return response(['error' => 'true', 'message' => 'The field Title is required'], 400);

        if((!isset($post['path']) || is_null($post['path'])))
            return response(['error' => 'true', 'message' => 'The field Path is required'], 400);

        try {
            return response(Posts::create($post), 201);
        } catch (\Exception $e) {
            return response('Bad Request', 400);
        }
    }

    /**
     * Método responsável pela edição de posts
     * @param $id
     * @param $post
     */
    public function update($id, $post)
    {
        try {
            return response(Posts::update($id, $post), 200);
        } catch (\Exception $e) {
            return response('Bad Request', 400);
        }
    }

    /**
     * Método responsável por deletar Posts
     * @param $id
     */
    public function delete($id)
    {
        try {
            return response(Posts::delete($id), 200);
        } catch (\Exception $e) {
            return response('Bad Request', 400);
        }
    }

    /**
     * Método responsável por retornar um Post específico
     * @param $id
     */
    public function find($id)
    {
        try {
            return response(Posts::find($id), 200);
        } catch (\Exception $e) {
            return response('Bad Request', 400);
        }
    }

    /**
     * Método responspável por retornar todos os Posts
     */
    public function findAll()
    {
        try {
            return response(Posts::findAll(), 200);
        } catch (\Exception $e) {
            return response('Bad Request', 400);
        }
    }
}