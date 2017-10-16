<?php

namespace App\Admin\Controller;

use Controller;
use View;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\Car\Model\H5Model;

class HomeController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    function test(){

        View::hamlReader('home','Admin');
    }

    function upd(){

        View::hamlReader('home/upd','Admin');
    }


    function index(){

        View::hamlReader('home','Admin');
    }

    function banner(){

        View::addData(['getList'=>'/home/admin_banner']);
        View::hamlReader('home/list','Admin');
    }

    function admin_h5_get(H5Model $model,$id){
        
        $this->L->adminPermissionCheck(122);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_h5_get',
            'upd'   => '../home/admin_h5_upd',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [
                'title' =>  '内容',
                'name'  =>  'content',
                'type'  =>  'h5',
            ]
                
                
                
                
            ];
            
        !$model->field && AJAX::error('字段没有公有化！');
            
            
        $info = AdminFunc::get($model,$id);
            
            
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    function admin_h5_upd(H5Model $model,$id){
        $this->L->adminPermissionCheck(122);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $app = $model->find($id);
        !$app && AJAX::error('error');


        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }

}