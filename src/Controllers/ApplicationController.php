<?php

/**
 * Application Controller.
 *
 * @author     Kaio Teixeira
 */

namespace TestJustCms\Controllers;
use TestJustCms\Models\Applications;

class ApplicationController
{
    /**
     * Método de autenticação de aplicações externas para acessarem os dados de Posts.
     * @param $data
     * @return null
     */
    public function auth($data)
    {
        $application = Applications::findByName($data['name']); //busca a application pelo nome

        if(!isset($application[0]))
            return null;

        if(!password_verify($data['password'], $application[0]->password))
            return null;

        return $application[0];
    }
}
