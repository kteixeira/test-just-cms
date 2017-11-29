<?php

namespace TestJustCms\Models;

class Posts extends Model
{
    protected  $fillable = [
        'id',
        'title',
        'body',
        'path',
        'created_at',
        'updated_at'
    ];

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = substr($title, 0, 120);
    }

    /**
     * @param $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = substr($path, 0, 170);
    }

    /**
     * @param $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = is_null($created_at)? date("Y-m-d H:i:s"): $created_at;
    }

    /**
     * @param $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = is_null($updated_at)? date("Y-m-d H:i:s"): $updated_at;
    }
}