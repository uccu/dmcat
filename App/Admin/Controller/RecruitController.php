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
use App\App\Model\RecruitModel;
use App\App\Model\AreaModel;


# 预约
class RecruitController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    function recruit(){

        View::addData(['getList'=>'admin_recruit']);
        View::hamlReader('home/list','Admin');
    }

    function admin_recruit(RecruitModel $model,$page = 1,$limit = 30,$status = -2,$search = ''){
        
        $this->L->adminPermissionCheck(141);

        $name = '商品';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../recruit/admin_recruit_get',
                'upd'   => '../recruit/admin_recruit_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../recruit/admin_recruit_del',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title' =>  '搜索',
                        'name'  =>  'search',
                        'size'  =>  4
                    ]   
                    
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '名字',
                '公司',
                '薪资',
                '电话',
                '开启'
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'company',
                'money',
                'phone',
                [
                    'name'=>'active',
                    'type'=>'checkbox'
                ]

            ];
            

        # 列表内容
        $where = [];


        if($search){

            $where['search'] = ['name LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->date = date('Y-m-d H:i:s',$v->create_time);
            $v->thumb_path = Func::fullPicAddr($v->thumb);
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
    function admin_recruit_get(RecruitModel $model,$id){
        
        $this->L->adminPermissionCheck(141);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../recruit/admin_recruit_get',
            'upd'   => '../recruit/admin_recruit_upd',
            'back'  => 'recruit/recruit',
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
            [
                'title' =>  '公司',
                'name'  =>  'company',
            ],
            [
                'title' =>  '薪资',
                'name'  =>  'money',
            ],
            [
                'title' =>  '电话',
                'name'  =>  'phone',
            ],
            [
                'title' =>  '电话',
                'name'  =>  'phone',
            ],
            [
                    
                'type'  =>  'selects',
                'url'   =>  '/home/area',
                'detail'=>[
                    ['name'=>'province_id' ,'title' =>  '省'],
                    ['name'=>'city_id'     ,'title' =>  '市'],
                    ['name'=>'distict_id'  ,'title' =>  '区']
                ]
            ],
            [
                'title' =>  '描述',
                'name'  =>  'description',
                'type'  =>  'h5',
            ],

                
                
                
            ];
            
        !$model->field && AJAX::error('字段没有公有化！');
        
            
        $info = AdminFunc::get($model,$id);
        
        $info->city_id = AreaModel::copyMutiInstance()->find($info->distict_id)->parent_id;
        $info->province_id = AreaModel::copyMutiInstance()->find($info->city_id)->parent_id;
            
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    function admin_recruit_upd(RecruitModel $model,$id){
        $this->L->adminPermissionCheck(141);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_recruit_del(RecruitModel $model,$id){
        $this->L->adminPermissionCheck(141);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




}