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
use App\App\Model\ClinicModel;
use App\App\Model\AreaModel;


# 门店
class ClinicController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    function clinic(){

        View::addData(['getList'=>'admin_clinic']);
        View::hamlReader('home/list','Admin');
    }

    function admin_clinic(ClinicModel $model,$page,$limit,$status = -2,$search = ''){
        
        $this->L->adminPermissionCheck(142);

        $name = '商品';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../clinic/admin_clinic_get',
                'upd'   => '../clinic/admin_clinic_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../clinic/admin_clinic_del',
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
                '缩略图',
                '电话',
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                [
                    'name'=>'thumb_path',
                    'type'=>'pic'
                ],
                'phone'

            ];
            

        # 列表内容
        $where = [];


        if($search){

            $where['search'] = ['name LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->get()->toArray();
        foreach($list as &$v){
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
    function admin_clinic_get(ClinicModel $model,$id){
        
        $this->L->adminPermissionCheck(142);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../clinic/admin_clinic_get',
            'upd'   => '../clinic/admin_clinic_upd',
            'back'  => 'clinic/clinic',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '商品名',
                'name'  =>  'name',
            ],
            [
                'title' =>  '缩略图',
                'name'  =>  'thumb',
                'type'  =>  'pic'
            ],
            [
                'title' =>  '大图',
                'name'  =>  'pic',
                'type'  =>  'picss',
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
                'title' =>  '详细地址',
                'name'  =>  'address',
            ],
            [
                'title' =>  '电话',
                'name'  =>  'phone',
            ],
            [
                'title' =>  '主营项目',
                'name'  =>  'project',
                'type'  =>  'textarea',
            ],
            [
                'title' =>  '诊所介绍',
                'name'  =>  'introduce',
                'type'  =>  'textarea',
            ],
            [
                'title' =>  '经度',
                'name'  =>  'longitude',
            ],
            [
                'title' =>  '纬度',
                'name'  =>  'latitude',
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
    function admin_clinic_upd(ClinicModel $model,$id){
        $this->L->adminPermissionCheck(142);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $data['pic'] = Request::getSingleInstance()->request('pic','raw');
        $data['pic'] = \implode(',',$data['pic']);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_clinic_del(ClinicModel $model,$id){
        $this->L->adminPermissionCheck(142);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

    /** 诊所列表
     * clinic_list
     * @param mixed $model 
     * @return mixed 
     */
    function clinic_list(ClinicModel $model){
        
        $this->L->adminPermissionCheck(75);

        $list = $model->get()->toArray();

        $list2 = [];

        foreach($list as $v){

            $list2[] = $v->id.'.'.$v->name;
        }


        # 输出内容
        $out = 
            [

                'list'  =>  $list2,

            
            ];

        AJAX::success($out);

    }



}