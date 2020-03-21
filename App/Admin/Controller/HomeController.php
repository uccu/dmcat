<?php

namespace App\Admin\Controller;

use View;
use Controller;
use App\Blog\Middleware\L;

class HomeController extends Controller
{


    function __construct()
    {

        $this->L = L::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'home';
    }

    function index()
    {

        View::hamlReader('home', 'Admin');
    }

    function upd()
    {

        View::hamlReader('home/upd', 'Admin');
    }
}
