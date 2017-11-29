<?php

namespace TestJustCms\Controllers;
use TestJustCms\Models\Applications;

class ApplicationController
{
    /**
     * @param $data
     * @return null
     */
    public function auth($data)
    {
        $application = Applications::findByName($data['name']);

        if(!isset($application[0]))
            return null;

        if(!password_verify($data['password'], $application[0]->password))
            return null;

        return $application[0];
    }
}
