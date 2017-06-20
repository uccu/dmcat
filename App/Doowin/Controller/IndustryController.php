<?php

namespace App\Doowin\Controller;


use App\Doowin\Model\StaticPageModel;


use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

class IndustryController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

    }


    # 德汇新天地
        function newWorld_get(StaticPageModel $model){
            $out['info'] = $model->find(5);
            AJAX::success($out);
        }
        function newWorld_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(5);
            AJAX::success();
        }
    # 万达广场
        function wandaSquare_get(StaticPageModel $model){
            $out['info'] = $model->find(6);
            AJAX::success($out);
        }
        function wandaSquare_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(6);
            AJAX::success();
        }
    # 亚欧新城
        function newCity_get(StaticPageModel $model){
            $out['info'] = $model->find(7);
            AJAX::success($out);
        }
        function newCity_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(7);
            AJAX::success();
        }
    # 德汇金融
        function finance_get(StaticPageModel $model){
            $out['info'] = $model->find(8);
            AJAX::success($out);
        }
        function finance_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(8);
            AJAX::success();
        }
    # 德汇教育
        function edu_get(StaticPageModel $model){
            $out['info'] = $model->find(9);
            AJAX::success($out);
        }
        function edu_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(9);
            AJAX::success();
        }
        
}
