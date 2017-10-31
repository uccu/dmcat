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
use App\App\Model\UserDateModel;


# 预约
class DateController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    function date(){

        View::addData(['getList'=>'admin_date']);
        View::hamlReader('home/list','Admin');
    }

    function admin_date(UserDateModel $model,$page,$limit,$status = -2){
        
        $this->L->adminPermissionCheck(139);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../date/admin_date_get',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title' =>  '状态',
                        'name'  =>  'status',
                        'type'  =>  'select',
                        'option'=>[
                            '-2'=>  '请选择',
                            '0' =>  '新预约',
                            '1' =>  '待确认',
                            '2' =>  '进行中',
                            '3' =>  '已完成',
                            '4' =>  '用户评价完成',
                            '-1'=>  '已取消',
                        ],
                        'default'=>'-2'
                    ]   
                    
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '医生',
                '用户',
                '门诊',
                '预约时间',
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'doctor_name',
                'user_name',
                'clinic_name',
                'date'

            ];
            

        # 列表内容
        $where = [];
        
        if($status != -2){
            $where['status'] = $status;
        }

        $list = $model->select('*','doctor.name>doctor_name','user.name>user_name','clinic.name>clinic_name')->order('create_time desc')->where($where)->get()->toArray();
        foreach($list as &$v){
            $v->date = date('Y-m-d H:i:s',$v->create_time);
        }


        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

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
    function admin_date_get(UserDateModel $model,$id){
        
        $this->L->adminPermissionCheck(139);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../date/admin_date_get',
            'upd'   => '../date/admin_date_upd',
            'back'  => 'date/date',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '用户',
                'name'  =>  'user_name',
                'disabled'=>true
            ],
            [
                'title' =>  '医生',
                'name'  =>  'doctor_name',
                'disabled'=>true
            ],
            [
                'title' =>  '诊所',
                'name'  =>  'clinic_name',
                'disabled'=>true
            ],
            [
                'title' =>  '状态',
                'name'  =>  'status',
                'type'  =>  'select',
                'option'=>[
                    '0' =>  '新预约',
                    '1' =>  '待确认',
                    '2' =>  '进行中',
                    '3' =>  '已完成',
                    '4' =>  '用户评价完成',
                    '-1'=>  '已取消',
                ]
            ],
            [
                'title' =>  '预约日期',
                'name'  =>  'date',
                'disabled'=>true
            ],
            [
                'title' =>  '用户评级',
                'name'  =>  'star',
                'disabled'=>true
            ],
            [
                'title' =>  '用户评价',
                'name'  =>  'comment',
                'type'  =>  'textarea',
                'disabled'=>true
            ],
            [
                'title' =>  '医生评价',
                'name'  =>  'content',
                'type'  =>  'textarea',
                'disabled'=>true
            ],
            [
                'title' =>  '医生给用户的标签',
                'name'  =>  'tags',
                'disabled'=>true
            ]
                
                
                
            ];
            
        !$model->field && AJAX::error('字段没有公有化！');
        
        $model->select('*','doctor.name>doctor_name','user.name>user_name','clinic.name>clinic_name')->order('create_time desc');
            
        $info = AdminFunc::get($model,$id);
        $info->date = $info->year.'-'.$info->month.'-'.$info->day;
            
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
    function admin_date_upd(UserDateModel $model,$id){
        $this->L->adminPermissionCheck(139);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_date_del(UserDateModel $model,$id){
        $this->L->adminPermissionCheck(139);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




}