<?php

namespace TestJustCms\Controllers;
use TestJustCms\Models\Posts;

class PostController
{
    private $posts;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        $this->posts = new Posts();
    }

    /**
     * @param $items
     * @return bool
     */
    public function create($items)
    {
        return $this->posts->create('posts', $items);
    }

    /**
     * @param $items
     * @return bool
     */
    public function update($items)
    {
        return $this->posts->update('posts', $items);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->posts->delete('posts', $id);
    }

    /**
     * @param $id
     * @return array
     */
    public function find($id)
    {
        return $this->posts->find('posts', $id);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->posts->findAll('posts');
    }
}