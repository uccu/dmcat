<?php

namespace App\Game\Controller;

use Controller;
use Uccu\DmcatTool\Tool\AJAX;
use App\Game\Model\UserModel;

use App\Resource\Tool\Func;

class ApiController extends Controller
{

    function __construct()
    {
        Func::visit_log();
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
        header('Access-Control-Allow-Headers:Origin,x-requested-with,content-type,Accept');
    }

    function login($name, $password, UserModel $model)
    {
        if (!$name || !$password) {
            AJAX::error('账号密码不能为空');
        }
        $user = $model->where(['name' => $name])->find();
        if (!$user) {
            AJAX::error('账号不存在');
        }

        if ($user->password !== $password) {
            AJAX::error('密码错误');
        }

        AJAX::success(['token' => md5($password . 'k-fq128hf')]);
    }


    function register($name, $password, UserModel $model)
    {
        if (!$name || !$password) {
            AJAX::error('账号密码不能为空');
        }

        $user = $model->where(['name' => $name])->find();
        if ($user) {
            AJAX::error('账号已存在');
        }

        $model->set(['name' => $name, 'password' => $password])->add();

        AJAX::success();
    }
}
