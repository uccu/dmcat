<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Lawyer\Middleware\L;
use App\Lawyer\Model\AdminMenuModel;
use App\Lawyer\Model\UserModel;

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
    function auth($id){

        $menu = $this->getMenu($id);
        View::addData(['menu'=>$menu]);
        View::addData(['id'=>$id]);
        View::hamlReader('staff/auth','Admin');
    }

    private function getMenu($id){

        $model = AdminMenuModel::copyMutiInstance();
        $uModel = UserModel::copyMutiInstance();
        $all = $model->order('id')->get('id')->toArray();

        $info = $uModel->find($id);
        !$info && AJAX::error('用户不存在');
        $auth = $info->type;
        $id = $info->id;

        $tops = [];
        foreach($all as $k=>$v){

            $v->auth = $v->auth ? explode(',',$v->auth) : [];
            $v->auth_user = $v->auth_user ? explode(',',$v->auth_user) : [];
            if(in_array($id,$v->auth_user)){
                $v->checked = 'checked';
            }else{
                $v->checked = '';
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



    function pay(){

        View::addData(['getList'=>'/money/admin_pay']);
        View::hamlReader('home/list','Admin');
    }

}