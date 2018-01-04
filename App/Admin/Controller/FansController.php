<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;
use DB;

# 数据模型
use App\Car\Model\UserModel;
use App\Car\Model\AdminModel;
use App\Car\Model\UserIncomeModel;
use App\Car\Model\DriverModel;
use App\Car\Model\DriverIncomeModel;
use App\Car\Model\AdminIncomeModel;
use App\Car\Model\DriverMoneyLogModel;
use App\Car\Model\AdminMoneyLogModel;
use App\Car\Model\UserMoneyLogModel;
use App\Car\Model\TripModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;


class FansController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controllerName = 'fans';

    }

    function fans($parent_id = 0,$search = ''){

        View::addData(['getList'=>'admin_'.__FUNCTION__.'?a=1&search='.$search.'&parent_id='.$parent_id]);
        View::hamlReader('home/list','Admin');
    }

    function admin_fans(UserModel $model,$page=1,$limit=10,$parent_id = 0,$search){

        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(160);

        # 允许操作接口
        // $m->setOpt('get','../'.$this->controllerName.'/'.__FUNCTION__.'_get');
        // $m->setOpt('del','../'.$this->controllerName.'/'.__FUNCTION__.'_del');
        // $m->setOpt('upd','../'.$this->controllerName.'/'.__FUNCTION__.'_upd');
        // $m->setOpt('view','home/upd');
        // $m->setOpt('add','home/upd');
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);


        # 设置表头
        $m->setHead('');
        $m->setHead('ID');
        $m->setHead('名字');
        $m->setHead('手机号');
        $m->setHead('推荐人数');
        $m->setHead('本周收入');
        $m->setHead('本月收入');
        $m->setHead('总收入');
        $m->setHead('余额');
        $m->setHead('推荐人');

        # 设置表体
        $m->setBody(['type'=>'pic','name'=>'avatar','size'=> "30"]);
        $m->setBody('id');
        $m->setBody('name');
        $m->setBody('phone');
        $m->setBody(['name'=>'count','href'=>true]);
        $m->setBody('week_income');
        $m->setBody('month_income');
        $m->setBody('all_income');
        $m->setBody(['name'=>'money','href'=>true]);
        $m->setBody(['name'=>'parent','href'=>true]);

        # 设置名字
        $m->setName();

        

        # 筛选
        $m->where = [];
        if($parent_id){
            $m->where['parent_id'] = $parent_id;
        }

        if($search){
            $m->where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        # 获取列表
        $model->select('id','avatar','name','phone','parent_id','money');
        $m->getList(0);

        $m->fullPicAddr('avatar');

        $week = TIME_TODAY - (date('w') - 1) * 24 * 3600;
        $userIncomeModel = UserIncomeModel::copyMutiInstance();

        $m->each(function(&$v) use ($model,$week,$userIncomeModel){
            $v->count = $model->where(['parent_id'=>$v->id])->select('COUNT(*) AS c','RAW')->find()->c;
            if($v->count)$v->count_href = 'fans/fans?parent_id='.$v->id;

            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['user_id'=>$v->id])->where('create_time>%n',$week)->find()->m;
            if(!$money)$money = '0.00';
            $v->week_income = $money;
            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['user_id'=>$v->id])->where(['month'=>date('Ym')])->find()->m;
            if(!$money)$money = '0.00';
            $v->month_income = $money;
            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['user_id'=>$v->id])->find()->m;
            if(!$money)$money = '0.00';
            $v->all_income = $money;

            if($v->parent_id && $parent = $model->find($v->parent_id)){
                $v->parent = $parent->name;
                $v->parent_href = 'fans/fans?search='.$parent->phone;
            }

            $v->money_href = 'fans/money?user_id='.$v->id;

        });


        $m->output();

    }



    function afans($search = ''){

        View::addData(['getList'=>'admin_'.__FUNCTION__.'?a=1&search='.$search]);
        View::hamlReader('home/list','Admin');
    }

    function admin_afans(AdminModel $model,$page=1,$limit=10,$search){

        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(162);

        # 允许操作接口

        $m->setOpt('view','home/upd');
        $m->setOpt('get','../staff/admin_admin_get');

        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);


        # 设置表头
        $m->setHead('');
        $m->setHead('ID');
        $m->setHead('名字');
        $m->setHead('账号');
        $m->setHead('已结算');
        $m->setHead('未结算余额');

        # 设置表体
        $m->setBody(['type'=>'pic','name'=>'avatar','size'=> "30"]);
        $m->setBody('id');
        $m->setBody('name');
        $m->setBody('phone');
        $m->setBody(['name'=>'history_profit','href'=>true]);
        $m->setBody('money');

        # 设置名字
        $m->setName();

        

        # 筛选
        $m->where = [];
        $m->where['type'] = ['type < 7'];

        if($search){
            $m->where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        # 获取列表
        $model->select('id','avatar','name','phone','money','history_profit');
        $m->getList(0);

        $m->fullPicAddr('avatar');

        $week = TIME_TODAY - (date('w') - 1) * 24 * 3600;
        $userIncomeModel = AdminIncomeModel::copyMutiInstance();
        $m->each(function(&$v) use ($model,$week,$userIncomeModel){


            $v->history_profit_href = 'fans/amoney?admin_id='.$v->id;

        });


        $m->output();

    }


    function dfans($parent_id = 0,$search = ''){

        View::addData(['getList'=>'admin_'.__FUNCTION__.'?a=1&search='.$search.'&parent_id='.$parent_id]);
        View::hamlReader('home/list','Admin');
    }

    function admin_dfans(DriverModel $model,$page=1,$limit=10,$parent_id = 0,$search){

        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(161);

        # 允许操作接口
        // $m->setOpt('get','../'.$this->controllerName.'/'.__FUNCTION__.'_get');
        // $m->setOpt('del','../'.$this->controllerName.'/'.__FUNCTION__.'_del');
        // $m->setOpt('upd','../'.$this->controllerName.'/'.__FUNCTION__.'_upd');
        // $m->setOpt('view','home/upd');
        // $m->setOpt('add','home/upd');
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);


        # 设置表头
        $m->setHead('');
        $m->setHead('ID');
        $m->setHead('名字');
        $m->setHead('手机号');
        $m->setHead('推荐人数');
        $m->setHead('本周收入');
        $m->setHead('本月收入');
        $m->setHead('总收入');
        $m->setHead('余额');
        $m->setHead('推荐人');

        # 设置表体
        $m->setBody(['type'=>'pic','name'=>'avatar','size'=> "30"]);
        $m->setBody('id');
        $m->setBody('name');
        $m->setBody('phone');
        $m->setBody(['name'=>'count','href'=>true]);
        $m->setBody('week_income');
        $m->setBody('month_income');
        $m->setBody('all_income');
        $m->setBody(['name'=>'money','href'=>true]);
        $m->setBody(['name'=>'parent','href'=>true]);

        # 设置名字
        $m->setName();

        

        # 筛选
        $m->where = [];
        if($parent_id){
            $m->where['parent_id'] = $parent_id;
        }

        if($search){
            $m->where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        # 获取列表
        $model->select('id','avatar','name','phone','parent_id','money');
        $m->getList(0);

        $m->fullPicAddr('avatar');

        $week = TIME_TODAY - (date('w') - 1) * 24 * 3600;
        $userIncomeModel = DriverIncomeModel::copyMutiInstance();

        $m->each(function(&$v) use ($model,$week,$userIncomeModel){
            $v->count = $model->where(['parent_id'=>$v->id])->select('COUNT(*) AS c','RAW')->find()->c;
            if($v->count)$v->count_href = 'fans/dfans?parent_id='.$v->id;

            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['driver_id'=>$v->id])->where('create_time>%n',$week)->find()->m;
            if(!$money)$money = '0.00';
            $v->week_income = $money;
            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['driver_id'=>$v->id])->where(['month'=>date('Ym')])->find()->m;
            if(!$money)$money = '0.00';
            $v->month_income = $money;
            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['driver_id'=>$v->id])->find()->m;
            if(!$money)$money = '0.00';
            $v->all_income = $money;

            if($v->parent_id && $parent = $model->find($v->parent_id)){
                $v->parent = $parent->name;
                $v->parent_href = 'fans/dfans?search='.$parent->phone;
            }

            $v->money_href = 'fans/dmoney?driver_id='.$v->id;

        });


        $m->output();

    }




    function dmoney($driver_id = 0){

        View::addData(['getList'=>'admin_'.__FUNCTION__.'?a=1&driver_id='.$driver_id]);
        View::hamlReader('home/list','Admin');
    }

    function admin_dmoney(DriverMoneyLogModel $model,$page=1,$limit=10,$driver_id = 0){

        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(161);

        $m->setOpt('del','../'.$this->controllerName.'/'.__FUNCTION__.'_del');

        # 设置表头
        $m->setHead('id');
        $m->setHead('内容');
        $m->setHead('金额');
        $m->setHead('时间');
        $m->setHead('备注');


        # 设置表体
        $m->setBody('id');
        $m->setBody('content');
        $m->setBody('money');
        $m->setBody('date');
        $m->setBody('remark');


        # 设置名字
        $m->setName();

        

        # 筛选
        $m->where = [];
        $m->where['driver_id'] = $driver_id;


        # 获取列表
        $model->order('id desc');
        $m->getList();



        $m->each(function(&$v) use ($model,$week,$userIncomeModel){
            
            $v->money  = $v->money > 0 ? '+' . $v->money : $v->money;

            $v->remark = $v->status == 0 ? '审核中':($v->status == -1 ? '审核失败' : '');

            $v->date = date('Y-m-d H:i:s',$v->create_time);

        });


        $m->output();

    }

    function admin_dmoney_del(DriverMoneyLogModel $model,$id){
        $this->L->adminPermissionCheck(161);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }
    

    function money($user_id = 0){

        View::addData(['getList'=>'admin_'.__FUNCTION__.'?a=1&user_id='.$user_id]);
        View::hamlReader('home/list','Admin');
    }
    function admin_money(UserMoneyLogModel $model,$page=1,$limit=10,$user_id = 0){

        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(160);
        $m->setOpt('del','../'.$this->controllerName.'/'.__FUNCTION__.'_del');

        # 设置表头
        $m->setHead('id');
        $m->setHead('内容');
        $m->setHead('金额');
        $m->setHead('时间');
        $m->setHead('备注');


        # 设置表体
        $m->setBody('id');
        $m->setBody('content');
        $m->setBody('money');
        $m->setBody('date');
        $m->setBody('remark');


        # 设置名字
        $m->setName();

        

        # 筛选
        $m->where = [];
        $m->where['user_id'] = $user_id;


        # 获取列表
        $model->order('id desc');
        $m->getList();



        $m->each(function(&$v) use ($model,$week,$userIncomeModel){
            
            $v->money  = $v->money > 0 ? '+' . $v->money : $v->money;

            $v->remark = $v->status == 0 ? '审核中':($v->status == -1 ? '审核失败' : '');

            $v->date = date('Y-m-d H:i:s',$v->create_time);

        });


        $m->output();

    }
    function admin_money_del(UserMoneyLogModel $model,$id){
        $this->L->adminPermissionCheck(161);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function amoney($admin_id = 0){

        View::addData(['getList'=>'admin_'.__FUNCTION__.'?a=1&admin_id='.$admin_id]);
        View::hamlReader('home/list','Admin');
    }
    function admin_amoney(AdminIncomeModel $model,$page=1,$limit=10,$admin_id = 0){

        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(160);
        $m->setOpt('del','../'.$this->controllerName.'/'.__FUNCTION__.'_del');

        # 设置表头
        $m->setHead('行程id');
        $m->setHead('收入');
        $m->setHead('收益');
        $m->setHead('订单类型');
        $m->setHead('订单');
        $m->setHead('时间');


        # 设置表体
        $m->setBody('trip_id');
        $m->setBody('money');
        $m->setBody('profit');
        $m->setBody('type');
        $m->setBody(['name'=>'order','href'=>true]);
        $m->setBody('date');


        # 设置名字
        $m->setName();

        

        # 筛选
        $m->where = [];
        $m->where['admin_id'] = $admin_id;


        # 获取列表
        $model->order('id desc');
        $m->getList();



        $m->each(function(&$v) use ($model,$week,$userIncomeModel){
            
            $v->money  = $v->money > 0 ? '+' . $v->money : $v->money;
            $v->profit  = $v->profit > 0 ? '+' . $v->profit : $v->profit;
            $v->type = '代驾';
            $v->date = date('Y-m-d H:i:s',$v->create_time);

            $trip = TripModel::copyMutiInstance()->find($v->trip_id);

            $v->order = '查看';
            $v->order_href = 'order/driving?id='.$trip->id;

        });


        $m->output();

    }
    function admin_amoney_del(AdminMoneyLogModel $model,$id){
        $this->L->adminPermissionCheck(161);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }
    
    
    
}
    