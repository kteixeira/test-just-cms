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
}