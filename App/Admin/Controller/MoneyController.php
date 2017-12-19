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
use App\Car\Model\PaymentModel;
use App\Car\Model\UserMoneyLogModel;
use App\Car\Model\UserModel;
use App\Car\Model\DriverModel;
use App\Car\Model\UserCouponModel;
use App\Car\Model\DriverMoneyLogModel;
use App\Car\Model\CouponModel;



class MoneyController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    function pay(){

        View::addData(['getList'=>'admin_pay']);
        View::hamlReader('home/list','Admin');
    }
    function coupon(){

        View::addData(['getList'=>'admin_coupon']);
        View::hamlReader('money/coupon','Admin');
    }
    function coupon_setting(){

        View::addData(['getList'=>'admin_coupon_setting']);
        View::hamlReader('home/list','Admin');
    }

    function cash_user(){

        View::addData(['getList'=>'admin_cash_user']);
        View::hamlReader('home/list','Admin');
    }

    function cash_driver(){

        View::addData(['getList'=>'admin_cash_driver']);
        View::hamlReader('home/list','Admin');
    }


    function admin_pay(PaymentModel $model,$page = 1,$limit = 20,$search = '',$ispaid = 0){
        
        $this->L->adminPermissionCheck(118);
        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../money/admin_pay_get',
                'upd'   => '../money/admin_pay_upd',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search'
                    ],
                    [
                        'title'=>'是否支付',
                        'name'=>'ispaid',
                        'type'=>'checkbox',
                        'default'=>'0'
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
                '支付金额',
                '支付方式',
                '支付时间',
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
                'user_name',
                'total_fee',
                'pay_type',
                'ispaid',
            ];
            
        # 列表内容
        $where = [];
        if($ispaid){
            $where['success_time'] = ['%F>0','success_time']; 
        }
        
        
        
        if($search){
            $where['search'] = ['%F LIKE %n OR %F LIKE %n','user.name','%'.$search.'%','user.phone','%'.$search.'%'];
        }
        $list = $model->select('user.name>user_name','user.avatar','user.phone','*')->order('ctime desc')->where($where)->page($page,$limit)->get()->toArray();
        $type_a = ['法律','留学转学','签证'];
        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->ispaid = $v->success_time ? date('Y-m-d H:i:s',$v->success_time):'未支付';
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
    function admin_pay_get(PaymentModel $model,$id){
        $this->L->adminPermissionCheck(118);
        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../money/admin_pay_get',
                'back'  => 'staff/pay',
                'view'  => 'home/upd',
            ];
        $tbody = 
            [  
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title'=>'订单号',
                    'name'=>'out_trade_no',
                    'disabled'=>true
                ],
                [
                    'title'=>'预付款ID（微信）',
                    'name'=>'prepay_id',
                    'disabled'=>true
                ],
                [
                    'title'=>'付款人',
                    'name'=>'name',
                    'disabled'=>true
                ],
                [
                    'title'=>'付款账号',
                    'name'=>'account',
                    'disabled'=>true
                ],
                [
                    'title'=>'open_id',
                    'name'=>'open_id',
                    'disabled'=>true
                ],
                [
                    'title'=>'第三方付款流水号',
                    'name'=>'open_order_id',
                    'disabled'=>true
                ],
                [
                    'title'=>'错误',
                    'name'=>'error',
                    'type'=>'textarea',
                    'disabled'=>true
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



    # 提现申请
    function admin_cash_user(UserMoneyLogModel $model,$page = 1,$limit = 10,$search,$type = -2){
        
        $this->L->adminPermissionCheck(120);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../money/admin_cash_user_get',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                    [
                        'title'=>'状态',
                        'name'=>'type',
                        'type'=>'select',
                        'option'=>[
                            '-2'=>'全部',
                            '-1'=>'未通过',
                            '0'=>'待审核',
                            '1'=>'已通过'
                        ],'default'=>'-2',
                        'size'=>'2'
                    ]
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '用户名',
                '提现金额',
                '姓名',
                '卡号',
                '银行',
                '分行',
                '状态',
                '申请时间'
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'money',
                'bname',
                'code',
                'bbname',
                'bank_name',
                'status_name',
                'date',

            ];
            

        # 列表内容
        $where = [];
        
        if($type != -2){
            $where['status'] = $type;
        }
        
        if($search){
            $where['search'] = ['user.name LIKE %n OR user.phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','user.name','userBank.code','userBank.name>bname','userBank.bank_name','userBank.bank_name','userBank.bank.name>bbname')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = [
                '0'=>'申请中',
                '1'=>'审核通过',
                '-1'=>'审核失败'
            ][$v->status];
            $v->date = date('Y-m-d H:i',$v->create_time);
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
    function admin_cash_user_get(UserMoneyLogModel $model,$id){
        
        $this->L->adminPermissionCheck(120);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../money/admin_cash_user_get',
            'upd'   => '../money/admin_cash_user_upd',
            'back'  => 'money/cash_user',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [
                'title' =>  '状态',
                'name'  =>  'status',
                'type'  =>  'select',
                'option'=>[
                    '0'=>'申请中',
                    '1'=>'审核通过',
                    '-1'=>'审核失败'
                ]
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
    function admin_cash_user_upd(UserMoneyLogModel $model,$id,$status){
        $this->L->adminPermissionCheck(120);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $app = $model->find($id);
        !$app && AJAX::error('error');

        if($app->status == 1){
            AJAX::error('已通过');
        }

        if($status == 1){
            
            $user = UserModel::copyMutiInstance()->find($app->user_id);
            if($user->money < $app->money)AJAX::error('用户余额不足');
            $user->money -= $app->money;
            $user->save();
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }

    # 司机提现申请
    function admin_cash_driver(DriverMoneyLogModel $model,$page = 1,$limit = 10,$search,$type = -2){
        
        $this->L->adminPermissionCheck(121);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../money/admin_cash_driver_get',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                    [
                        'title'=>'状态',
                        'name'=>'type',
                        'type'=>'select',
                        'option'=>[
                            '-2'=>'全部',
                            '-1'=>'未通过',
                            '0'=>'待审核',
                            '1'=>'已通过'
                        ],'default'=>'-2',
                        'size'=>'2'
                    ]
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',
                '提现金额',
                '状态',
                '申请时间'
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'money',
                'status_name',
                'date',

            ];
            

        # 列表内容
        $where = [];
        
        if($type != -2){
            $where['status'] = $type;
        }
        
        if($search){
            $where['search'] = ['driver.name LIKE %n OR driver.phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','driver.name')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = [
                '0'=>'申请中',
                '1'=>'审核通过',
                '-1'=>'审核失败'
            ][$v->status];
            $v->date = date('Y-m-d H:i',$v->create_time);
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
    function admin_cash_driver_get(DriverMoneyLogModel $model,$id){
        
        $this->L->adminPermissionCheck(121);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../money/admin_cash_driver_get',
            'upd'   => '../money/admin_cash_driver_upd',
            'back'  => 'money/cash_driver',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [
                'title' =>  '状态',
                'name'  =>  'status',
                'type'  =>  'select',
                'option'=>[
                    '0'=>'申请中',
                    '1'=>'审核通过',
                    '-1'=>'审核失败'
                ]
            ],
            [
                'title' =>  '备注',
                'name'  =>  'mes',
                'type'  =>  'textarea',

            ],
                
                
                
                
            ];
            
        !$model->field && AJAX::error('字段没有公有化！');
            
            
        $info = AdminFunc::get($model,$id);
        if($info->status != 0)$tbody[1]['disabled'] = true;
        if($info->status != 0)$tbody[2]['disabled'] = true;
        if($info->status != 0)unset($opt['upd']);
            
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
    function admin_cash_driver_upd(DriverMoneyLogModel $model,$id,$status){
        $this->L->adminPermissionCheck(121);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $app = $model->find($id);
        !$app && AJAX::error('error');

        if($app->status == 1){
            AJAX::error('已通过');
        }

        if($status == 1){
            
            $user = DriverModel::copyMutiInstance()->find($app->driver_id);
            if($user->money < $app->money)AJAX::error('司机余额不足');
            $user->money -= $app->money;
            $user->save();
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }


    # 优惠券
    function admin_coupon(UserModel $model,$search){

        $this->L->adminPermissionCheck(127);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [


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

    function admin_coupon_send(UserModel $model,CouponModel $cmodel,$search,$coupon_id = 0){

        $this->L->adminPermissionCheck(127);

        !$coupon_id && AJAX::error('请选择优惠券');
        $coupon = $cmodel->find($coupon_id);
        !$coupon && AJAX::error('请选择优惠券');


        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->where($where)->get()->toArray();


        $data['end_time'] = $coupon->end_time;
        $data['money'] = $coupon->money;
        $data['type'] = $coupon->type;
        $data['get_time']  = TIME_NOW;
        
        $model = UserCouponModel::copyMutiInstance();

        foreach($list as $v){

            $data['user_id'] = $v->id;
            $model->set($data)->add();

        }

        AJAX::success();

    }



    function admin_coupon_setting(CouponModel $model,$page = 1,$limit = 10,$type = 0){
        
        $this->L->adminPermissionCheck(129);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../money/admin_coupon_setting_get',
                'upd'   => '../money/admin_coupon_setting_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../money/admin_coupon_setting_del',
                'view'  => 'home/upd',
                'req'   =>[

                    [
                        'title'=>'类型',
                        'name'=>'type',
                        'type'=>'select',
                        'option'=>[
                            '0'=>'全部',
                            '1'=>'代驾',
                            '2'=>'出租车',
                            '3'=>'顺风车'
                        ],'default'=>'0',
                        'size'=>'2'
                    ]
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '优惠券金额',
                '到期时间',
                '类型',
                
            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'money',
                'end_date',
                'type_name',

            ];
            

        # 列表内容
        $where = [];
        
        if($type){
            $where['type'] = $type;
        }


        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->type_name = [
                '1'=>'代驾',
                '2'=>'出租车',
                '3'=>'顺风车'
            ][$v->type];
            $v->end_date = date('Y-m-d H:i:s',$v->end_time);
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
    function admin_coupon_setting_get(CouponModel $model,$id){
        
        $this->L->adminPermissionCheck(129);
        $name = '优惠券';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../money/admin_coupon_setting_get',
            'upd'   => '../money/admin_coupon_setting_upd',
            'back'  => 'money/coupon_setting',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [
                'title' =>  '类型',
                'name'  =>  'type',
                'type'  =>  'select',
                'option'=>[
                    '1'=>'代驾',
                    '2'=>'出租车',
                    '3'=>'顺风车'
                ],'default'=>'1'
            ],
            [
                'title'     =>  '金额',
                'name'      =>  'money',
                'default'   =>'0.00'
            ],
            [
                'title'     =>  '到期时间',
                'name'      =>  'end_date',
                'type'      =>  'laydate',
                'default'   =>  date('Y-m-d')
            ],
                
                
                
                
            ];
            
        !$model->field && AJAX::error('字段没有公有化！');
            
            
        $info = AdminFunc::get($model,$id);
        if($info->end_time)$info->end_date = date('Y-m-d',$info->end_time);

            
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    function admin_coupon_setting_upd(CouponModel $model,$id,$end_date){
        $this->L->adminPermissionCheck(129);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        $data['end_time'] = strtotime($end_date) + 3600 * 24 - 1;
        if(!$data['money'] || $data['money'] == 0)AJAX::error('金额设置为0！');
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }

}