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
use App\Car\Model\UserModel;
use App\Car\Model\DriverModel;
use App\Car\Model\AdminModel;
use App\Car\Model\AreaModel;
use App\Car\Model\JudgeModel;


class StaffController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    public function encrypt_password($password,$salt){
        return sha1($this->salt.md5($password).$salt);
    }

    /*  用户 */
    function user(){

        View::addData(['getList'=>'admin_user']);
        View::hamlReader('home/list','Admin');
    }
    /* 顺风车司机 */
    function suser(){

        View::addData(['getList'=>'admin_suser']);
        View::hamlReader('home/list','Admin');
    }
    /*  司机 */
    function driver(){

        View::addData(['getList'=>'admin_driver']);
        View::hamlReader('home/list','Admin');
    }

    /*  管理员 */
    function admin(){

        View::addData(['getList'=>'admin_admin']);
        View::hamlReader('home/list','Admin');
    }

    function judge_driver($id){

        View::addData(['getList'=>'admin_judge_driver?id='.$id]);
        View::hamlReader('home/list','Admin');

    }
    function judge($id){

        View::addData(['getList'=>'admin_judge?id='.$id]);
        View::hamlReader('home/list','Admin');

    }


    # 管理用户
    function admin_user(UserModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(68);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_user_get',
                'upd'   => '../staff/admin_user_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_user_del',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '手机号',
                '名字',
                '启用',


            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'id',
                'phone',
                'name',
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],


            ];
            

        # 列表内容
        $where = [];
        
        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
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
    function admin_user_get(UserModel $model,$id){

        $this->L->adminPermissionCheck(68);
        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_user_get',
                'upd'   => '../staff/admin_user_upd',
                'back'  => 'staff/user',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_user_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '手机号',
                    'name'  =>  'phone',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'avatar',
                    'type'  =>  'avatar',
                ],
                
                [
                    'title' =>  '修改密码',
                    'name'  =>  'pwd',
                    'size'  =>  '4',
                ],
                
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

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
    function admin_user_upd(UserModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(68);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['type']);
        unset($data['salt']);
        unset($data['id']);

        $model->where('phone = %n AND id != %d',$data['phone'],$id)->find() && AJAX::error('手机号已存在，请更改为其他手机号！');

        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
            $data['create_time'] = TIME_NOW;
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_user_del(UserModel $model,$id){
        $this->L->adminPermissionCheck(68);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




    # 管理司机
    function admin_driver(DriverModel $model,$page = 1,$limit = 10,$search,$typee,$longitude,$latitude){
        
        $this->L->adminPermissionCheck(75);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_driver_get',
                'upd'   => '../staff/admin_driver_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_driver_del',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                    [
                        'title'=>'代驾',
                        'name'=>'typee',
                        'type'=>'select',
                        'size'=>'2',
                        'option'=>[
                            '0'=>'全部',
                            '1'=>'代驾',
                            '2'=>'出租车'
                        ],'default'=>'0'
                    ],

                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '手机号',
                '名字',
                '启用',
                '评价',

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'id',
                'phone',
                'name',
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],
                [
                    'name'=>'judge',
                    'href'=>true
                ]


            ];
            

        # 列表内容
        $where = [];

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%c)','city_id',explode(',', $this->L->userInfo->city_id)];
        }

        if($typee == 1)$where['type_driving'] = 1;
        elseif($typee == 2)$where['type_taxi'] = 1;
        
        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }


        if($longitude){

            $model->select(['*,ABS(%F-%f) + ABS(%F-%f) AS `mul`,online.latitude,online.longitude','online.latitude',$latitude,'online.longitude',$longitude],'RAW')->order('mul desc','RAW');
        }else{
            $model->order('create_time desc');
        }


        $list = $model->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->judge = '查看';
            $v->judge_href = 'staff/judge_driver?id='.$v->id;
            if($longitude){

                $v->dis = Func::getSDistance($v->latitude,$v->longitude,$latitude,$longitude);
                if($v->dis < 1000)$v->dis = $v->dis.'米';
                else $v->dis = number_format( $v->dis/1000,1,'.','').'公里';
            }
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
    function admin_driver_get(DriverModel $model,$id){

        $this->L->adminPermissionCheck(75);
        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_driver_get',
                'upd'   => '../staff/admin_driver_upd',
                'back'  => 'staff/driver',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_driver_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '手机号',
                    'name'  =>  'phone',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'avatar',
                    'type'  =>  'avatar',
                ],
                [
                    'title' =>  '类型',
                    'name'  =>  'typee',
                    'type'  =>  'select',
                    'option'=>[
                        '0'=>'请选择',
                        '1'=>'代驾',
                        '2'=>'出租车'
                    ],'default'=>'0'
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
                    'title' =>  '品牌',
                    'name'  =>  'brand',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '车牌',
                    'name'  =>  'car_number',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '修改密码',
                    'name'  =>  'pwd',
                    'size'  =>  '4',
                ],
                
                

            ];

        if($this->L->userInfo->type == 2 || $this->L->userInfo->type == 1){
            unset($tbody[4]);
        }

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);
        $info->province_id = AreaModel::copyMutiInstance()->find($info->city_id)->parent_id;

        $info->typee = $info->type_driving ? 1:2;

        if(!$info->province_id){
            $info->province_id = '';
        }

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
    function admin_driver_upd(DriverModel $model,$id,$pwd,$typee,$active){
        $this->L->adminPermissionCheck(75);
        !$model->field && AJAX::error('字段没有公有化！');
        
        $data = Request::getSingleInstance()->request($model->field);

        if(is_null($active)){
            if(!$typee)AJAX::error('请选择类型');
            if($typee == 1){
                $data['type_driving'] = 1;
                $data['type_taxi'] = 0;
            }else{
                $data['type_driving'] = 0;
                $data['type_taxi'] = 1;
            }
            if(!$data['city_id'])AJAX::error('请选择城市');
        }
        

        
        unset($data['type']);
        unset($data['salt']);
        unset($data['id']);

        $model->where('phone = %n AND id != %d',$data['phone'],$id)->find() && AJAX::error('手机号已存在，请更改为其他手机号！');

        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
            $data['create_time'] = TIME_NOW;
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_driver_del(DriverModel $model,$id){
        $this->L->adminPermissionCheck(75);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

    # 管理管理员
    function admin_admin(AdminModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(67);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_admin_get',
                'upd'   => '../staff/admin_admin_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_admin_del',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '手机号',
                '名字',
                '启用',


            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'id',
                'phone',
                'name',
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],


            ];
            

        # 列表内容
        $where = [];
        $where['type'] = ['type < 7'];
        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
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
    function admin_admin_get(AdminModel $model,$id){

        $this->L->adminPermissionCheck(67);
        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_admin_get',
                'upd'   => '../staff/admin_admin_upd',
                'back'  => 'staff/admin',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_admin_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '手机号',
                    'name'  =>  'phone',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'avatar',
                    'type'  =>  'avatar',
                ],
                [
                    
                    'type'  =>  'selects',
                    'url'   =>  '/home/area',
                    'detail'=>[
                        ['name'=>'province_id' ,'title' =>  '管理省'],
                        ['name'=>'city_id'     ,'title' =>  '管理市','all'=>true,'type'=>'checkboxs','size'=>'8']
                    ]
                ],
                [
                    'title' =>  '修改密码',
                    'name'  =>  'pwd',
                    'size'  =>  '4',
                ],
                
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

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
    function admin_admin_upd(AdminModel $model,AreaModel $aModel,$id,$pwd,$active,$city_id){
        $this->L->adminPermissionCheck(67);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['salt']);
        unset($data['id']);

        if(!$id || !is_numeric($active)){
            if($data['province_id'])$data['type'] = 2;
            else AJAX::error('请选择管理的省');
            if($data['city_id'])$data['type'] = 1;
            $model->where('phone = %n AND id != %d',$data['phone'],$id)->find() && AJAX::error('手机号已存在，请更改为其他手机号！');
        }
        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
            $data['create_time'] = TIME_NOW;
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        if($city_id){

            $ids = explode(',',$city_id);

            foreach($ids as $i){
                if($model->where('city_id LIKE %n AND id != %n','%'.$i.'%',$id)->find()){
                    $name = $aModel->find($i)->areaName;
                    AJAX::error('`'.$name.'`已经有人管理！');
                }
                
            }
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_admin_del(AdminModel $model,$id){
        $this->L->adminPermissionCheck(67);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }



    
    # 管理顺风车司机
    function admin_suser(UserModel $model,$page = 1,$limit = 10,$search,$longitude,$latitude){
        
        $this->L->adminPermissionCheck(117);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_suser_get',
                'upd'   => '../staff/admin_suser_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_suser_del',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '手机号',
                '名字',
                '启用',
                '评价'


            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'id',
                'phone',
                'name',
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],
                [
                    'name'=>'judge',
                    'href'=>true
                ]


            ];
            

        # 列表内容
        $where = [];
        $where['type'] = 1;
        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%c)','city_id', explode(',', $this->L->userInfo->city_id)];
        }
        
        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        if($longitude){

            $model->select(['*,ABS(%F-%f) + ABS(%F-%f) AS `mul`,online.latitude,online.longitude','online.latitude',$latitude,'online.longitude',$longitude],'RAW')->order('mul desc','RAW');
        }else{
            $model->order('create_time desc');
        }

        

        $list = $model->where($where)->page($page,$limit)->get()->toArray();
        // echo $model->sql;die();
        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->judge = '查看';
            $v->judge_href = 'staff/judge?id='.$v->id;
            if($longitude){

                $v->dis = Func::getSDistance($v->latitude,$v->longitude,$latitude,$longitude);
                if($v->dis < 1000)$v->dis = $v->dis.'米';
                else $v->dis = number_format( $v->dis/1000,1,'.','').'公里';
            }
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
    function admin_suser_get(UserModel $model,$id){

        $this->L->adminPermissionCheck(117);
        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_suser_get',
                'upd'   => '../staff/admin_suser_upd',
                'back'  => 'staff/user',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_suser_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '手机号',
                    'name'  =>  'phone',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'avatar',
                    'type'  =>  'avatar',
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
                    'title' =>  '品牌',
                    'name'  =>  'brand',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '车牌',
                    'name'  =>  'car_number',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '修改密码',
                    'name'  =>  'pwd',
                    'size'  =>  '4',
                ],
                
                

            ];
        
        // if($this->L->userInfo->type == 2 || $this->L->userInfo->type == 1){
        //     unset($tbody[4]);
        // }
        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);
        $info->province_id = AreaModel::copyMutiInstance()->find($info->city_id)->parent_id;

        if(!$info->province_id){
            $info->province_id = '';
        }
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
    function admin_suser_upd(UserModel $model,$id,$pwd,$active){
        $this->L->adminPermissionCheck(117);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['salt']);
        unset($data['id']);

        if(is_null($active)){
            if(!$data['city_id'])AJAX::error('请选择城市');
            $model->where('phone = %n AND id != %d',$data['phone'],$id)->find() && AJAX::error('手机号已存在，请更改为其他手机号！');
        }
        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
            $data['create_time'] = TIME_NOW;
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_suser_del(UserModel $model,$id){
        $this->L->adminPermissionCheck(117);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    # 评价
    function admin_judge_driver(JudgeModel $model,$page = 1,$limit = 5,$id){
        
        $this->L->adminPermissionCheck(75);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '乘客ID',
                '手机号',
                '名字',
                '星级',
                '标签',
                '评价',
                '评价时间',

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'user_id',
                'phone',
                'name',
                'score',
                'tag',
                'comment',
                'date'


            ];
            

        # 列表内容
        $where = [];

        $where['driver_id'] = $id;
        $where['type'] = ['type<3'];

        $list = $model->select('*','user.name','user.avatar','user.phone')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->judge = '查看';
            $v->judge_href = 'staff/judge_driver?id='.$v->id;
            $v->date = date('Y-m-d H:i:s');
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


    function admin_judge(JudgeModel $model,$page = 1,$limit = 5,$id){
        
        $this->L->adminPermissionCheck(75);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '乘客ID',
                '手机号',
                '名字',
                '星级',
                '标签',
                '评价',
                '评价时间'

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'user_id',
                'phone',
                'name',
                'score',
                'tag',
                'comment',
                'date'


            ];
            

        # 列表内容
        $where = [];

        $where['driver_id'] = $id;
        $where['type'] = 3;

        $list = $model->select('*','user.name','user.avatar','user.phone')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->judge = '查看';
            $v->judge_href = 'staff/judge_driver?id='.$v->id;
            $v->date = date('Y-m-d H:i:s');
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

}