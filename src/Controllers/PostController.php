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
            return response(['error' => 'true', 'message' => 'The field Title is required']);

        if((!isset($post['path']) || is_null($post['path'])))
            return response(['error' => 'true', 'message' => 'The field Path is required']);

        return response(Posts::create($post));
    }

    /**
     * Método responsável pela edição de posts
     * @param $id
     * @param $post
     */
    public function update($id, $post)
    {
        return response(Posts::update($id, $post));
    }

    /**
     * Método responsável por deletar Posts
     * @param $id
     */
    public function delete($id)
    {
        return response(Posts::delete($id));
    }

    /**
     * Método responsável por retornar um Post específico
     * @param $id
     */
    public function find($id)
    {
        return response(Posts::find($id));
    }

    /**
     * Método responspável por retornar todos os Posts
     */
    public function findAll()
    {
        return response(Posts::findAll());
    }
}