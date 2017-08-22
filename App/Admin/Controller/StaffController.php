<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Lawyer\Middleware\L;
use App\Lawyer\Model\AdminMenuModel;

class StaffController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();
        

    }

    /* 管理员 */
    function master(){

        View::addData(['getList'=>'/user/admin_master']);
        View::hamlReader('home/list','Admin');
    }

    /*  用户 */
    function user(){

        View::addData(['getList'=>'/user/admin_user']);
        View::hamlReader('home/list','Admin');
    }


    /*  用户 */
    function profit(){

        View::addData(['getList'=>'/money/admin_user']);
        View::hamlReader('home/list','Admin');
    }

    function profit_detail($id){

        View::addData(['getList'=>'/money/admin_user_detail?id='.$id]);
        View::hamlReader('home/list','Admin');
    }


    /*  律师 */
    function lawyer(){

        View::addData(['getList'=>'/lawyer/admin_lawyer']);
        View::hamlReader('home/list','Admin');
    }
    
    /* 审批 */
    function apply(){

        View::addData(['getList'=>'/user/admin_apply']);
        View::hamlReader('home/list','Admin');
    }

    /* 权限 */
    function auth(){

        $menu = $this->getMenu();
        View::addData(['menu'=>$menu]);
        View::hamlReader('staff/auth','Admin');
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