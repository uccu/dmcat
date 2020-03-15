<?php

namespace App\Project\Controller;

use Controller;

use Uccu\DmcatTool\Tool\AJAX;
use App\Project\Model\UserModel;
use Uccu\DmcatTool\Tool\LocalConfig;


use View;


class TestController extends Controller
{


    function __construct()
    {
    }



    function main($cc)
    {

        echo 'ok';
    }

    function ec(UserModel $model)
    {

        $model = UserModel::clone();
        echo $model->select('id>user','friend.friend_id','name')->get('name');
        // var_dump($data);
    }


    function getLessionById($name = null, $id = null)
    {
        //var_dump(func_get_args());
        //echo '123';
        // echo Lession::copyMutiInstance()->where('id=%d', 1)->get();
    }

    function haml()
    {
        View::addData(['g' => ['title' => 'zz', 'keywords' => 'baka']]);
        View::hamlReader('Test/my', 'App');
    }

    function test(UserModel $model)
    {
        $user = $model->find(1);
        echo $user;
    }

    function b()
    {
        $a = LocalConfig::get('HOOK_CLASS');
        $s = class_exists($a);
        $g = method_exists($a, 'ajaxCallback');
        $a::ajaxCallback();
        AJAX::error();
    }
}
