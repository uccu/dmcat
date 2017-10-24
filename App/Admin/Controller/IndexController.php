<?php

namespace App\Admin\Controller;

use Controller;
use View;
use Uccu\DmcatTool\Tool\AJAX;
use Response;
use App\App\Middleware\L3;
use App\App\Model\AdminMenuModel;

class IndexController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();

        if(!$this->L->id){
            header('Location:/adminApi/logout');die();
        }

        $menu = $this->getMenu();

        
        View::addData(['menu'=>$menu]);
        View::addData(['userInfo'=>$this->L->userInfo]);

        View::hamlReader('index','Admin');
    }

    private function getMenu(){

        $model = AdminMenuModel::copyMutiInstance();
        $all = $model->order('id')->get('id')->toArray();

        $auth = $this->L->userInfo->type;
        $id = $this->L->id;

        $tops = [];
        foreach($all as $k=>$v){

            $v->auth = $v->auth ? explode(',',$v->auth) : [];
            $v->auth_user = $v->auth_user ? explode(',',$v->auth_user) : [];
            if(!in_array($auth,$v->auth) && !in_array($id,$v->auth_user)){
                unset($all[$k]);continue;
            }

            $v->list = [];
            if(!$v->top){
                $tops[$v->id] = $v;
                unset($all[$k]);
            }
        }
        foreach($all as $k=>$v){

            if($all[$v->top]){
                $all[$v->top]->list[] = $v;
            }
        }
        
        foreach($all as $k=>$v){

            if( $tops[$v->top] ){

                $tops[$v->top]->list[] = $v;
            }
        }
        // AJAX::success($tops);
        return $tops;

    }



    


}