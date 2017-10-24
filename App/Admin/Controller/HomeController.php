<?php

namespace App\Admin\Controller;

use Controller;
use View;
use Request;
use App\App\Middleware\L3;
use App\App\Tool\Func;
use App\App\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\App\Model\H5Model;
use App\App\Model\BankModel;
use App\App\Model\TagModel;
use App\App\Model\UserFeedbackModel;
use App\App\Model\DoctorFeedbackModel;

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



    function user_feedback(){

        View::addData(['getList'=>'admin_user_feedback']);
        View::hamlReader('home/list','Admin');
    }

    function doctor_feedback(){

        View::addData(['getList'=>'admin_doctor_feedback']);
        View::hamlReader('home/list','Admin');
    }


    function admin_user_feedback(UserFeedbackModel $model){
        
        $this->L->adminPermissionCheck(132);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_user_feedback_get',
                'view'  => 'home/upd',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '名字',
                '反馈时间'
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'date'

            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->select('*','user.name')->order('id')->where($where)->get()->toArray();
        foreach($list as &$v){
            $v->date = date('Y-m-d H:i:s',$v->create_time);
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
    function admin_user_feedback_get(UserFeedbackModel $model,$id){
        
        $this->L->adminPermissionCheck(132);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_user_feedback_get',
            'back'  => 'home/user_feedback',
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
                'type'  =>  'textarea',
                'disabled'=>true

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
    function admin_user_feedback_upd(UserFeedbackModel $model,$id){
        $this->L->adminPermissionCheck(132);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_user_feedback_del(UserFeedbackModel $model,$id){
        $this->L->adminPermissionCheck(132);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function admin_doctor_feedback(DoctorFeedbackModel $model){
        
        $this->L->adminPermissionCheck(133);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_doctor_feedback_get',
                'view'  => 'home/upd',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '名字',
                '反馈时间'
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'date'

            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->select('*','doctor.name')->order('create_time desc')->where($where)->get()->toArray();
        foreach($list as &$v){
            $v->date = date('Y-m-d H:i:s',$v->create_time);
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
    function admin_doctor_feedback_get(DoctorFeedbackModel $model,$id){
        
        $this->L->adminPermissionCheck(133);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_doctor_feedback_get',
            'back'  => 'home/doctor_feedback',
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
                'type'  =>  'textarea',
                'disabled'=>true
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
    function admin_doctor_feedback_upd(DoctorFeedbackModel $model,$id){
        $this->L->adminPermissionCheck(133);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_doctor_feedback_del(DoctorFeedbackModel $model,$id){
        $this->L->adminPermissionCheck(133);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

}