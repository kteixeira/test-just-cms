<?php

namespace TestJustCms\Controllers;
use TestJustCms\Models\Posts;

class PostController
{
    /**
     * @param $post
     */
    public function create($post)
    {
        if((!isset($post['title']) || is_null($post['title'])) || (!isset($post['path']) || is_null($post['path'])))
            return response(['error' => 'true', 'message' => 'The title and path values are required']);

        return response(Posts::create($post));
    }

    /**
     * @param $id
     * @param $post
     */
    public function update($id, $post)
    {
        return response(Posts::update($id, $post));
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        return response(Posts::delete($id));
    }

    /**
     * @param $id
     */
    public function find($id)
    {
        return response(Posts::find($id));
    }

    /**
     *
     */
    public function findAll()
    {
        return response(Posts::findAll());
    }
}