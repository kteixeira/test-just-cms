<?php

namespace TestJustCms\Models;

class Posts extends Model
{
    private $title;
    private $body;
    private $path;
    private $created_at;
    private $updated_at;

    function getTitle()
    {
        return $this->title;
    }

    function getBody()
    {
        return $this->body;
    }

    function getPath()
    {
        return $this->path;
    }

    function getCreatedAt()
    {
        return $this->created_at;
    }

    function getUpdatedAt()
    {
        return $this->updated_at;
    }

    function setTitle($title)
    {
        $this->title = $title;
    }

    function setBody($body)
    {
        $this->body = $body;
    }

    function setPath($path)
    {
        $this->path = $path;
    }

    function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
}