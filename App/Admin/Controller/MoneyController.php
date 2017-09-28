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



class MoneyController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    function pay(){

        View::addData(['getList'=>'admin_pay']);
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
}