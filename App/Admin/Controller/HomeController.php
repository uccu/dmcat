<?php

namespace App\Admin\Controller;

use Controller;
use View;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;
use fengqi\Hanzi\Hanzi;
use Model;

# 数据模型
use App\Car\Model\H5Model;
use App\Car\Model\BankModel;
use App\Car\Model\TagModel;
use App\Car\Model\FeedbackModel;
use App\Car\Model\DriverFeedbackModel;
use App\Car\Model\BrandModel;
use App\Car\Model\ColorModel;
use App\Car\Model\QuestionModel;
use App\Car\Model\ConfigModel;
use App\Car\Model\UsedCarModel;
use App\Car\Model\AreaModel;

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


    # 版本控制
    function admin_driver_version_get($id){
        
        $this->L->adminPermissionCheck(149);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_driver_version_get',
            'upd'   => '../home/admin_driver_version_upd',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [
                'title' =>  '版本号',
                'name'  =>  'driver_version',
                'size'  =>  '3'
            ],
            [
                'title' =>  '安装文件',
                'name'  =>  'driver_version_file',
                'type'  =>  'file'
            ]
                
                
                
                
            ];
            
            
            
        $info['driver_version'] = $this->L->config->driver_version;
        $info['driver_version_file'] = $this->L->config->driver_version_file;
        
        if($info['driver_version_file']){

            $tbody[2]['description'] = Func::fullAddr('download/getVersionFile_driver');
        }
            
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    function admin_driver_version_upd($id,ConfigModel $model,$driver_version,$driver_version_file){
        $this->L->adminPermissionCheck(149);

        $upd = $model->set(['value'=>$driver_version,'name'=>'driver_version'])->add(true);
        $upd = $model->set(['value'=>$driver_version_file,'name'=>'driver_version_file'])->add(true);
        
        $out['upd'] = $upd;
        AJAX::success($out);
    }


    function admin_user_version_get($id){
        
        $this->L->adminPermissionCheck(149);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_user_version_get',
            'upd'   => '../home/admin_user_version_upd',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [
                'title' =>  '版本号',
                'name'  =>  'user_version',
                'size'  =>  '3'
            ],
            [
                'title' =>  '安装文件',
                'name'  =>  'user_version_file',
                'type'  =>  'file',
                'description'=>''
            ]
                
                
                
                
            ];
            
            
            
        $info['user_version'] = $this->L->config->user_version;
        $info['user_version_file'] = $this->L->config->user_version_file;

        if($info['user_version_file']){

            $tbody[2]['description'] = Func::fullAddr('download/getVersionFile_user');
        }
            
            
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    function admin_user_version_upd($id,ConfigModel $model,$user_version,$user_version_file){
        $this->L->adminPermissionCheck(149);

        $upd = $model->set(['value'=>$user_version,'name'=>'user_version'])->add(true);
        $upd = $model->set(['value'=>$user_version_file,'name'=>'user_version_file'])->add(true);
        
        $out['upd'] = $upd;
        AJAX::success($out);
    }



    function bank(){

        View::addData(['getList'=>'admin_bank']);
        View::hamlReader('home/list','Admin');
    }

    function admin_bank(BankModel $model,$page=1,$limit=20){
        
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

    function admin_tag(TagModel $model,$page=1,$limit=20){
        
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

    function user_feedback(){

        View::addData(['getList'=>'admin_user_feedback']);
        View::hamlReader('home/list','Admin');
    }

    function driver_feedback(){

        View::addData(['getList'=>'admin_driver_feedback']);
        View::hamlReader('home/list','Admin');
    }


    function admin_user_feedback(FeedbackModel $model,$limit=10,$page=1){
        
        $this->L->adminPermissionCheck(132);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_user_feedback_get',
                // 'view'  => 'home/upd',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '名字',
                '反馈内容',
                '反馈时间'
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'content',
                'date'

            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->select('*','user.name')->order('id')->where($where)->get()->toArray();
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
    function admin_user_feedback_get(FeedbackModel $model,$id){
        
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
    function admin_user_feedback_upd(FeedbackModel $model,$id){
        $this->L->adminPermissionCheck(132);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_user_feedback_del(FeedbackModel $model,$id){
        $this->L->adminPermissionCheck(132);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function admin_driver_feedback(DriverFeedbackModel $model,$page=1,$limit=10){
        
        $this->L->adminPermissionCheck(133);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_driver_feedback_get',
                // 'view'  => 'home/upd',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '名字',
                '反馈内容',
                '反馈时间'
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'content',
                'date'

            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->select('*','driver.name')->order('create_time desc')->where($where)->get()->toArray();
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
    function admin_driver_feedback_get(DriverFeedbackModel $model,$id){
        
        $this->L->adminPermissionCheck(133);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_driver_feedback_get',
            'back'  => 'home/driver_feedback',
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
    function admin_driver_feedback_upd(DriverFeedbackModel $model,$id){
        $this->L->adminPermissionCheck(133);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_driver_feedback_del(DriverFeedbackModel $model,$id){
        $this->L->adminPermissionCheck(133);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }



    function brand(){

        View::addData(['getList'=>'admin_brand']);
        View::hamlReader('home/list','Admin');
    }

    function admin_brand(BrandModel $model,$page=1,$limit=10){
        
        $this->L->adminPermissionCheck(136);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_brand_get',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../home/admin_brand_del',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '名字',
                '缩略图'
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                

            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->order('pinyin')->where($where)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = Func::fullPicAddr($v->thumb);
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
    function admin_brand_get(BrandModel $model,$id){
        
        $this->L->adminPermissionCheck(136);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_brand_get',
            'upd'   => '../home/admin_brand_upd',
            'back'  => 'home/brand',
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
                'size'  =>  '2'
            ],
            [
                'title' =>  '缩略图',
                'type'  =>  'pic',
                'name'  =>  'thumb',
            ],
            [
                'title' =>  '车型',
                'type'  =>  'option',
                'name'  =>  'modelx',
                'size'  =>  '2'
            ],
            
                
                
                
                
        ];
            
        !$model->field && AJAX::error('字段没有公有化！');
            
            
        $info = AdminFunc::get($model,$id);
        
        $info->modelx = Model::copyMutiInstance('car_model')->where(['brand_id'=>$info->id])->order('pinyin')->get()->toArray();
            
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
    function admin_brand_upd(BrandModel $model,$id,$name){
        $this->L->adminPermissionCheck(136);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $pinyin = Hanzi::pinyin($name);
        $data['pinyin'] = $pinyin['pinyin'];
        $data['first'] = strtoupper(substr( $pinyin['pinyin'],0,1));

        $upd = AdminFunc::upd($model,$id,$data);

        !$id && $id = $upd;

        $modelx = Request::getSingleInstance()->request('modelx','raw');

        $model2 = Model::copyMutiInstance('car_model');
        
        $model2->where(['brand_id'=>$id])->remove();


        if($modelx)foreach($modelx as $v){
            $pinyin = Hanzi::pinyin($v)['pinyin'];
            $model2->set(['brand_id'=>$id,'name'=>$v,'pinyin'=>$pinyin])->add();
        }
        
        
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_brand_del(BrandModel $model,$id){
        $this->L->adminPermissionCheck(136);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function color(){

        View::addData(['getList'=>'admin_color']);
        View::hamlReader('home/list','Admin');
    }
    function admin_color(ColorModel $model,$page=1,$limit =10){
        
        $this->L->adminPermissionCheck(137);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_color_get',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../home/admin_color_del',
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
        

        $list = $model->order('pinyin')->where($where)->get()->toArray();



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
    function admin_color_get(ColorModel $model,$id){
        
        $this->L->adminPermissionCheck(137);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_color_get',
            'upd'   => '../home/admin_color_upd',
            'back'  => 'home/color',
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
                'size'  =>  '2'
            ],

                
                
                
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
    function admin_color_upd(ColorModel $model,$id,$name){
        $this->L->adminPermissionCheck(137);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);
        $pinyin = Hanzi::pinyin($name);
        $data['pinyin'] = $pinyin['pinyin'];
        $upd = AdminFunc::upd($model,$id,$data);
   
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_color_del(ColorModel $model,$id){
        $this->L->adminPermissionCheck(137);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }



    function question(){

        View::addData(['getList'=>'admin_question']);
        View::hamlReader('home/list','Admin');
    }
    function admin_question(QuestionModel $model,$page = 1,$limit = 10){
        
        $this->L->adminPermissionCheck(139);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_question_get',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../home/admin_question_del',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '问题',
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->where($where)->get()->toArray();



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
    function admin_question_get(QuestionModel $model,$id){
        
        $this->L->adminPermissionCheck(139);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_question_get',
            'upd'   => '../home/admin_question_upd',
            'back'  => 'home/question',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '问题',
                'name'  =>  'name',
                'size'  =>  '2'
            ],
            [
                'title' =>  '回答',
                'name'  =>  'content',
                'type'  =>  'h5'
            ],

                
                
                
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
    function admin_question_upd(QuestionModel $model,$id,$name){
        $this->L->adminPermissionCheck(139);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);
        $upd = AdminFunc::upd($model,$id,$data);
   
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_question_del(QuestionModel $model,$id){
        $this->L->adminPermissionCheck(139);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }






    function used_car(){

        View::addData(['getList'=>'admin_used_car']);
        View::hamlReader('home/list','Admin');
    }
    function admin_used_car(UsedCarModel $model,$status = 0,$page = 1,$limit = 10){
        
        $this->L->adminPermissionCheck(152);

        $name = '二手车';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_used_car_get',
                'upd'   => '../home/admin_used_car_upd',
                'view'  => 'home/upd',
                // 'add'   => 'home/upd',
                'del'   => '../home/admin_used_car_del',
                'req'   =>[

                    [
                        'type'=>'checkbox',
                        'name'=>'status',
                        'title'=>'通过'
                    ]

                ]
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '用户',
                '车牌',
                '上牌时间',
                '城市',
                '电话',
                '售价(万)',
                '里程（公里）',
                '状态'
            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',
                'brade',
                'add_time',
                'city_name',
                'phone',
                'price',
                'distance',
                [
                    'name'=>'status',
                    'type'=>'checkbox'
                ]
            ];
            

        # 列表内容
        $where = [];
        $where['status']  = $status;

        $list = $model->select('*','user.name','city.areaName>city_name')->where($where)->get()->toArray();



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
    function admin_used_car_get(UsedCarModel $model,$id){
        
        $this->L->adminPermissionCheck(152);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_used_car_get',
            'upd'   => '../home/admin_used_car_upd',
            'back'  => 'home/used_car',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '车牌',
                'name'  =>  'brade',
                'size'  =>  '3'
            ],
            [
                'title' =>  '上牌时间',
                'name'  =>  'add_time',
                'size'  =>  '3'
            ],
            [
                    
                'type'  =>  'selects',
                'url'   =>  '/home/area',
                'detail'=>[
                    ['name'=>'province_id' ,'title' =>  '省'],
                    ['name'=>'city_id'     ,'title' =>  '市','all'=>true]
                ]
            ],
            [
                'title' =>  '电话',
                'name'  =>  'phone',
                'size'  =>  '2'
            ],
            [
                'title' =>  '售价(万)',
                'name'  =>  'price',
                'size'  =>  '2'
            ],
            [
                'title' =>  '里程（公里）',
                'name'  =>  'distance',
                'size'  =>  '2'
            ],
            
            [
                'title' =>  '详情',
                'name'  =>  'distance',
                'size'  =>  '6',
                'type'  =>  'textarea'
            ],
            [
                'title' =>  '图片',
                'name'  =>  'pic',
                'type'  =>  'picss',
                'size'  =>  '6'
                
            ],
            [
                'title' =>  '状态',
                'name'  =>  'status',
                'type'  =>  'select',
                'option'=>[
                    '0'=>'未通过',
                    '1'=>'已通过',
                ]
            ],
                
                
        ];
            
        !$model->field && AJAX::error('字段没有公有化！');
            
            
        $info = AdminFunc::get($model,$id);

        $info->province_id = AreaModel::copyMutiInstance()->find($info->city_id)->parent_id;

        if(!$info->province_id){
            $info->province_id = '';
        }
        
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    function admin_used_car_upd(UsedCarModel $model,$id,$name){
        $this->L->adminPermissionCheck(152);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $pic = Request::getSingleInstance()->request('pic','raw');
        if($pic)$data['pic'] = implode(',',$pic);

        // var_dump($pic);die();

        $upd = AdminFunc::upd($model,$id,$data);
   
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_used_car_del(UsedCarModel $model,$id){
        $this->L->adminPermissionCheck(152);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

}