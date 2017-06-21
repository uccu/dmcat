<?php

namespace App\Doowin\Controller;
use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

use App\Doowin\Model\StaticPageModel;



require_once(BASE_ROOT.'App/Doowin/Middleware/Lang.php');

class AppHomeDomainController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        
    }

    function newWorld(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇宝贝广场';
        $page = $pageModel->find(5);
        include_once(VIEW_ROOT.'App/Domain_wandaSquare.php');

    }
    function wandaSquare(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇万达广场';
        $page = $pageModel->find(6);
        include_once(VIEW_ROOT.'App/Domain_wandaSquare.php');

    }
    function newCity(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇特色小镇';
        $page = $pageModel->find(7);
        include_once(VIEW_ROOT.'App/Domain_wandaSquare.php');

    }
    function finance(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇金融';
        $page = $pageModel->find(8);
        include_once(VIEW_ROOT.'App/Domain_wandaSquare.php');

    }
    function edu(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇教育';
        $page = $pageModel->find(9);
        include_once(VIEW_ROOT.'App/Domain_wandaSquare.php');

    }
    function logistics(StaticPageModel $pageModel){
        
        $type = __FUNCTION__;
        $name = '德汇物流';
        $page = $pageModel->find(10);
        include_once(VIEW_ROOT.'App/Domain_wandaSquare.php');

    }
    
}
