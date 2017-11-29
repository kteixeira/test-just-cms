<?php

namespace TestJustCms\Models;

class Applications extends Model
{
    protected  $fillable = [
        'id',
        'name',
        'password',
        'key',
        'created_at',
        'updated_at',
        'expire_token'
    ];
}