<?php

namespace App\Admin\Controller;

use Controller;
use View;
use App\Blog\Middleware\L;


class LoginController extends Controller
{

    function __construct()
    {
        $this->L = L::getSingleInstance();
        if ($this->L->id) {
            header('Location:/admin/index');
            die();
        }

        View::hamlReader('login', 'Admin');
    }
}
