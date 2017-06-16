<?php

namespace App\Admin\Controller;

use Controller;
use View;
use App\Doowin\Middleware\L;
use App\Doowin\Model\AdminMenuModel;

class IndexController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

        if(!$this->L->id || $this->L->userInfo->type<1){
            header('Location:/user/logout');
        }

        $menu = $this->getMenu();

        
        View::addData(['menu'=>$menu]);
        View::addData(['userInfo'=>$this->L->userInfo]);

        View::hamlReader('index','Admin');
    }

    private function getMenu(){

        $model = AdminMenuModel::getInstance();
        $all = $model->order('id')->get()->toArray();

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
                $tops[$v->id] = $v;unset($all[$k]);
            }
        }
        
        foreach($all as $k=>$v){

            if( $tops[$v->top] ){

                $tops[$v->top]->list[] = $v;
            }
        }

        return $tops;

    }



    


}