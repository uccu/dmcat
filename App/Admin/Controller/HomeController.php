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
use App\Car\Model\BankModel;
use App\Car\Model\TagModel;

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

    function bank(){

        View::addData(['getList'=>'admin_bank']);
        View::hamlReader('home/list','Admin');
    }

    function admin_bank(BankModel $model){
        
        $this->L->adminPermissionCheck(126);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_bank_get',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../home/admin_bank_del',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '名字',
                '图片',
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                [
                    'name'=>'thumb',
                    'type'=>'pic'
                ]

            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->order('id')->where($where)->get()->toArray();
        foreach($list as &$v){
            $v->thumb = Func::fullPicAddr($v->thumb);
        }


        # 分页内容
        $page   = 1;
        $max    = count($list);
        $limit  = count($list);

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_bank_get(BankModel $model,$id){
        
        $this->L->adminPermissionCheck(126);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_bank_get',
            'upd'   => '../home/admin_bank_upd',
            'back'  => 'home/bank',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [
                'title' =>  '图片',
                'name'  =>  'thumb',
                'type'  =>  'pic',

            ],
            [
                'title' =>  '名字',
                'name'  =>  'name',

            ],
                
                
                
                
            ];
            
        !$model->field && AJAX::error('字段没有公有化！');
            
            
        $info = AdminFunc::get($model,$id);
        if($info->status != 0)$tbody[1]['disabled'] = true;
            
        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;
            
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    function admin_bank_upd(BankModel $model,$id){
        $this->L->adminPermissionCheck(126);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_bank_del(BankModel $model,$id){
        $this->L->adminPermissionCheck(126);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




    function tag(){

        View::addData(['getList'=>'admin_tag']);
        View::hamlReader('home/list','Admin');
    }

    function admin_tag(TagModel $model){
        
        $this->L->adminPermissionCheck(130);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_tag_get',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../home/admin_tag_del',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '名字',
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',


            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->order('id')->where($where)->get()->toArray();
        foreach($list as &$v){
            $v->thumb = Func::fullPicAddr($v->thumb);
        }


        # 分页内容
        $page   = 1;
        $max    = count($list);
        $limit  = count($list);

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_tag_get(TagModel $model,$id){
        
        $this->L->adminPermissionCheck(130);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_tag_get',
            'upd'   => '../home/admin_tag_upd',
            'back'  => 'home/tag',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '名字',
                'name'  =>  'name',

            ],
                
                
                
                
            ];
            
        !$model->field && AJAX::error('字段没有公有化！');
            
            
        $info = AdminFunc::get($model,$id);
        if($info->status != 0)$tbody[1]['disabled'] = true;
            
        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;
            
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    function admin_tag_upd(TagModel $model,$id){
        $this->L->adminPermissionCheck(130);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_tag_del(TagModel $model,$id){
        $this->L->adminPermissionCheck(130);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

}