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
use DB;

use App\Car\Model\DriverCancelReasonModel;
use App\Car\Model\AdminMoneyLogModel;
use App\Car\Model\BankModel;


use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

class DriverController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        

    }

    function cancel_reason(){

        View::addData(['getList'=>'admin_cancel_reason']);
        View::hamlReader('home/list','Admin');
    }

    function admin_cancel_reason(DriverCancelReasonModel $model,$page=1,$limit=20){
        
        $this->L->adminPermissionCheck(158);

        $name = '取消理由';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../driver/admin_cancel_reason_get',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../driver/admin_cancel_reason_del',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '理由',
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'msg',


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
    function admin_cancel_reason_get(DriverCancelReasonModel $model,$id){
        
        $this->L->adminPermissionCheck(158);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../driver/admin_cancel_reason_get',
            'upd'   => '../driver/admin_cancel_reason_upd',
            'back'  => 'driver/cancel_reason',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '理由',
                'name'  =>  'msg',

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
    function admin_cancel_reason_upd(DriverCancelReasonModel $model,$id){
        $this->L->adminPermissionCheck(158);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_cancel_reason_del(DriverCancelReasonModel $model,$id){
        $this->L->adminPermissionCheck(158);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function admin_cash_get(AdminMoneyLogModel $model,BankModel $bankModel,$id = 0){

        $m = Gets::getSingleInstance($model,$id);

        # 权限
        $m->checkPermission(167);

        # 允许操作接口
        $m->setOpt('get','../driver/admin_cash_get');
        $m->setOpt('upd','../driver/admin_cash_upd');
        $m->setOpt('view','driver/upd');
        $m->setOpt('back','money/cash_admin');

        # 设置表体
        $m->setBody(['title'=>'当前余额','name'=>'balance','size'=>2,'disabled'=>true]);
        $m->setBody(['title'=>'姓名','name'=>'name','size'=>2]);
        $s = $m->setBody(['title'=>'银行','name'=>'bank_id','type'=>'select','default'=>'0']);
        $m->setBody(['title'=>'卡号','name'=>'code']);
        $m->setBody(['title'=>'分行全称','name'=>'bank_name']);
        $m->setBody(['title'=>'提现金额','name'=>'money','size'=>2,'default'=>'0.00']);
        $m->setBody(['title'=>'备注','name'=>'mes','type'=>'textarea']);
        

        $m->tbody[$s]['option'] = $bankModel->get_field('name','id');
        $m->tbody[$s]['option']['0'] = '请选择';

        # 设置名字
        $m->setName($m->getInfo()->title);

        $m->info->balance = $this->L->userInfo->balance;
        
        # 输出
        $m->output();
            
    }
    function admin_cash_upd(AdminMoneyLogModel $model,$id = 0,$money,$code,$bank_name,$name){
        $this->L->adminPermissionCheck(167);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        if($money <= 0){
            AJAX::error('请填写提现金额');
        }
        if(!$code){
            AJAX::error('请填写卡号');
        }
        if(!$bank_name){
            AJAX::error('请填写分行全称');
        }
        if(!$name){
            AJAX::error('请填写姓名');
        }
        
        $data['money'] = -$money;
        $data['create_time'] = TIME_NOW;
        $data['admin_id'] = $this->L->id;
        $data['content'] = '提现';

        if(!$data['bank_id'])AJAX::error('请选择银行');


        DB::start();

        $this->L->userInfo->balance -= $money;
        $this->L->userInfo->balance < 0 && AJAX::error('余额不足');
        $this->L->userInfo->save();

        $upd = AdminFunc::upd($model,$id,$data);

        DB::commit();
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    

}