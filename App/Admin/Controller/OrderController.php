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
use App\Car\Model\OrderDrivingModel;
use App\Car\Model\OrderTaxiModel;
use App\Car\Model\OrderWayModel;
use App\Car\Model\UserApplyModel;
use App\Car\Model\DriverApplyModel;
use App\Car\Model\DriverModel;
use App\Car\Model\UserModel;
use App\Car\Model\TripModel; 
use App\Car\Model\StatusModel; 
use App\Car\Model\AreaModel;
use App\Car\Model\DriverIncomeModel;
use App\Car\Model\UserIncomeModel;
use App\Car\Model\AdminIncomeModel;
use App\Car\Model\DriverOnlineModel;
use App\Car\Model\DriverServingPositionModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;


class OrderController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }


    /*  代驾 */
    function driving($id = 0){

        View::addData(['getList'=>'admin_driving?id='.$id]);
        View::hamlReader('home/list','Admin');
    }
    /* 出租车 */
    function taxi(){

        View::addData(['getList'=>'admin_taxi']);
        View::hamlReader('home/list','Admin');
    }
    /*  顺风车 */
    function way(){

        View::addData(['getList'=>'admin_way']);
        View::hamlReader('home/list','Admin');
    }
    function apply(){

        View::addData(['getList'=>'admin_apply']);
        View::hamlReader('home/list','Admin');
    }
    function driver_apply(){

        View::addData(['getList'=>'admin_driver_apply']);
        View::hamlReader('home/list','Admin');
    }


    function upd(){

        View::hamlReader('order/upd','Admin');
    }


    function upd2(){

        View::hamlReader('order/upd2','Admin');
    }
    


    # 管理代驾订单
    function admin_driving(OrderDrivingModel $model,TripModel $tripModel,$id = 0,$page = 1,$limit = 50,$search,$status = -1){
        
        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(113);

        # 允许操作接口
        $m->setOpt('get','../order/admin_driving_get');
        $m->setOpt('upd','../order/admin_driving_upd');
        $m->setOpt('del','../order/admin_driving_del');
        $m->setOpt('view','order/upd');
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);
        $reqC = $m->setOptReq(['title'=>'状态','name'=>'status','type'=>'select','default'=>'-1']);

        $statusArr = StatusModel::copyMutiInstance()->get_field('msg','id')->toArray();
        $m->opt['req'][$reqC]['option'] = $statusArr;
        $m->opt['req'][$reqC]['option']['-1'] = '请选择';

        # 设置名字
        $m->setName('代驾订单');
        

        # 设置表头
        $m->setHead('ID');
        $m->setHead('行程ID');
        $m->setHead('用户');
        $m->setHead('司机');
        $m->setHead('状态');
        $m->setHead('起点');
        $m->setHead('终点');
        $m->setHead('预估价(元)');
        $m->setHead('总价(元)');
        $m->setHead('司机收入');
        $m->setHead('用户收入');
        $m->setHead('线路');

        # 设置表体
        $m->setBody('id');
        $m->setBody('trip_id');
        $m->setBody('user_name');
        $m->setBody('driver_name');
        $m->setBody('status_name');
        $m->setBody('start_name');
        $m->setBody('end_name');
        $m->setBody('estimated_price');
        $m->setBody('total_fee');
        $m->setBody(['name'=>'driver_income','href'=>true]);
        $m->setBody(['name'=>'user_income','href'=>true]);
        $m->setBody(['name'=>'route','href'=>true]);

        # 筛选
        $m->where = [];
        if($id)$m->where['id'] = $id;
        if($status != -1)$m->where['statuss'] = $status;

        if($this->L->userInfo->type == 2){
            $m->where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $m->where['city_id'] = ['%F IN (%c)','city_id', explode(',', $this->L->userInfo->city_id)];
        }
        $search && $m->where['search'] = ['start_name LIKE %n OR end_name LIKE %n OR user.name LIKE %n OR driver.name LIKE %n OR user.phone LIKE %n OR driver.phone LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%'];

        # 获取列表
        $model->select('*','user.name>user_name','driver.name>driver_name')->order('create_time desc');
        $m->getList(0);

        $m->fetchArr('status_name','statuss',$statusArr);

        $m->each(function(&$v) use ($tripModel){
            $trip = $tripModel->where(['id'=>$v->id,'type'=>1])->find();
            $v->trip_id = $trip->trip_id;
            if($v->statuss > 49 && ($trip->pay_type == 1 || $trip->pay_type == 2)){
                $v->driver_income = '查看';
                $v->driver_income_href = 'order/driver_income?trip_id='.$trip->trip_id;
                $v->user_income = '查看';
                $v->user_income_href = 'order/user_income?trip_id='.$trip->trip_id;   
            }
            $v->route = '查看';
            $v->route_href = 'order/map?trip_id='.$trip->trip_id;

        });

        $m->output();

    }
    function admin_driving_get(OrderDrivingModel $model,$id){


        $m = Gets::getSingleInstance($model,$id);

        # 权限
        $m->checkPermission(113);

        # 允许操作接口
        $m->setOpt('get','../order/admin_driving_get');
        $m->setOpt('back','order/driving');
        $m->setOpt('upd','../order/admin_driving_upd');
        $m->setOpt('view','home/upd');

        # 设置表体
        $m->setBody(['type'  =>  'hidden','name'  =>  'id']);
        $m->setBody(['type'  =>  'hidden','name'  =>  'trip_id']);
        $staC = $m->setBody(['title'=>'状态','name'=>'statuss','type'=>'select','default'=>'0','description'=>'<font style="color:red">（一般情况下，不能修改）</font>']);
        
        $m->setBody(['title' =>  '联系人电话（代叫限定）','name'  =>  'phone','size'  =>  '2',]);
        $m->setBody(['title' =>  '联系人称呼（代叫限定）','name'  =>  'name','size'  =>  '2',]);
        $m->setBody(['title' =>  '用户','name'  =>  'user_name','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '用户手机号','name'  =>  'user_phone','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '司机','name'  =>  'driver_name','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '司机手机号','name'  =>  'driver_phone','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '开始地址名字','name'  =>  'start_name','size'  =>  '4','disabled'=>true]);
        $m->setBody(['title' =>  '结束地址名字','name'  =>  'end_name','size'  =>  '4','disabled'=>true]);
        $m->setBody(['title' =>  '实时里程(公里)','name'  =>  'real_distance','size'  =>  '4','disabled'=>true]);
        $m->setBody(['title' =>  '预估价(元)','name'  =>  'estimated_price','size'  =>  '4']);
        $m->setBody(['title' =>  '服务开始时间','name'  =>  'start_time_date','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '服务结束时间','name'  =>  'end_time_date','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '城市','name'  =>  'city_name','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '起步价(元)','name'  =>  'start_fee','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '里程价格(元)','name'  =>  'way_fee','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '等待费用(元)','name'  =>  'lay_fee','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '其他费用(元)','name'  =>  'other_fee_content','size'  =>  '4','type'  =>  'textarea','disabled'=>true]);
        $m->setBody(['title' =>  '总价格(元)','name'  =>  'total_fee','size'  =>  '2']);
        $staD = $m->setBody(['title' =>'派单','name'  =>'toDriver','size'  =>'4','suggest'=>'/admin/staff/admin_driver?typee=1&search=','fields'=>['id'=>'id','name'=>'名字','phone'=>'手机号','brand'=>'品牌','car_number'=>'车牌号','dis'=>'距离'],'button'=>'派']);

        # 设置名字
        $m->setName();

        
        $m->getInfo();


        $statusArr = StatusModel::copyMutiInstance()->get_field('msg','id')->toArray();
        $m->tbody[$staC]['option'] = $statusArr;

        if($m->info){
            $trip = TripModel::copyMutiInstance()->select('*','cancelType.name>cancel_type_name')->where(['id'=>$id,'type'=>1])->find();
        }

        # 司机和用户名字如果有的话
        if($m->info->user_id){
            $m->info->user_name = UserModel::copyMutiInstance()->find($m->info->user_id)->name;
            $m->info->user_phone = UserModel::copyMutiInstance()->find($m->info->user_id)->phone;
        }
        if($m->info->driver_id){
            $m->info->driver_name = DriverModel::copyMutiInstance()->find($m->info->driver_id)->name;
            $m->info->driver_phone = DriverModel::copyMutiInstance()->find($m->info->driver_id)->phone;
        }
        # 服务开始时间、服务结束时间、起步费、真实行程距离、行程id、行程费用
        $m->info->start_time_date = $trip->in_time ? date('Y-m-d H:i:s',$trip->in_time) : '';
        $m->info->end_time_date = $trip->out_time ? date('Y-m-d H:i:s',$trip->out_time) : '';
        $m->info->start_fee = $trip->start_fee;
        $m->info->real_distance = $trip->real_distance / 1000;
        $m->info->trip_id = $trip->trip_id;
        $m->info->way_fee = $m->info->fee != 0?number_format($m->info->fee - $m->info->start_fee,2,'.',''):'0.00';


        # 显示其他费用
        $contents = json_decode($trip->other_fee);
        if($contents){
            foreach($contents as $content)
            $m->info->other_fee_content .= $content->type .'：￥'. $content->price."\n";
        }

        $city = AreaModel::copyMutiInstance()->find($m->info->city_id);
        if($city){
            $province = AreaModel::copyMutiInstance()->find($city->parent_id);
        }
        if(!$city || !$province){
            $m->info->city_name = '';
        }else{
            $m->info->city_name = $province->areaName. ' ' .$city->areaName;
        }

        if($m->info->statuss != 10){
            $m->tbody[$staD]['disabled'] = true;
            $m->tbody[$staD]['suggest'] = '';
        }else{
            $m->tbody[$staD]['suggest'] = '/admin/staff/admin_driver?typee=1&latitude='.$m->info->start_latitude.'&longitude='.$m->info->start_longitude.'&search=';
        }

        $m->info->cancelReason = $trip->cancel_type_name.' '.$trip->cancel_reason;

        if($m->info->statuss == 0){
            $m->setBody(['title' =>  '取消原因','name'  =>  'cancelReason','type'  =>  'textarea','disabled'=>true]);
        }

        # 输出
        $m->output();

    }
    function admin_driving_upd(OrderDrivingModel $model,$id,$toDriver,DriverModel $driverModel,$statuss,TripModel $tripModel){
        $this->L->adminPermissionCheck(113);
        
        $app = $model->find($id);
        !$app && AJAX::error('订单不存在');

        $trip = $tripModel->where(['type'=>1,'id'=>$id])->find();
        !$trip && AJAX::error('行程不存在');

        DB::start();

        // Func::push_driver($toDriver,'平台已指定派单，请接乘客。',['type'=>'driving']);
        $data = Request::getSingleInstance()->request($model->field);
        $upd = AdminFunc::upd($model,$id,$data);

        if($statuss != $app->statuss){
            $app->statuss = $statuss;
            $app->save();
            $trip->statuss = $statuss;
            $trip->save();
        }
        
        if($toDriver){
            $driver = $driverModel->find($toDriver);
            !$driver && AJAX::error('司机不存在');
            

            if(!in_array($statuss,[5,10])){
                AJAX::error('该订单无法派单');
            }

            

            $app->driver_id = $driver->id;
            $app->statuss = 20;
            $app->save();
            $trip->driver_id = $driver->id;
            $trip->statuss = 20;
            $trip->save();

            
        }

        DB::commit();

        $out['upd'] = 1;
        AJAX::success($out);
    }
    function admin_driving_del(OrderDrivingModel $model,$id){
        $this->L->adminPermissionCheck(113);
        $del = AdminFunc::del($model,$id);
        TripModel::copyMutiInstance()->where(['id'=>$id,'type'=>1])->remove();
        $out['del'] = $del;
        AJAX::success($out);
    }

    function driver_income($trip_id = 0){

        View::addData(['getList'=>'admin_driver_income?trip_id='.$trip_id]);
        View::hamlReader('home/list','Admin');
    }
    function admin_driver_income(DriverIncomeModel $model,$page = 1,$limit = 50,$trip_id = 0){
        
        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(113);

        # 允许操作接口

        # 设置名字
        $m->setName('代驾订单');
        

        # 设置表头
        $m->setHead('司机');
        $m->setHead('手机号');
        $m->setHead('收益');
        $m->setHead('等级');


        # 设置表体
        $m->setBody('driver_name');
        $m->setBody('phone');
        $m->setBody('money');
        $m->setBody('level');


        # 筛选
        $m->where = [];
        $m->where['trip_id'] = $trip_id;

        # 获取列表
        $model->select('*','driver.phone','driver.name>driver_name')->order('level');
        $m->getList(1);

        $m->output();

    }

    function user_income($trip_id = 0){

        View::addData(['getList'=>'admin_user_income?trip_id='.$trip_id]);
        View::hamlReader('home/list','Admin');
    }
    function admin_user_income(UserIncomeModel $model,$page = 1,$limit = 50,$trip_id = 0){
        
        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(113);

        # 允许操作接口

        # 设置名字
        $m->setName('代驾订单');
        

        # 设置表头
        $m->setHead('用户');
        $m->setHead('收益');
        $m->setHead('等级');


        # 设置表体
        $m->setBody('id');
        $m->setBody('user_name');
        $m->setBody('level');


        # 筛选
        $m->where = [];
        $m->where['trip_id'] = $trip_id;

        # 获取列表
        $model->select('*','user.name>user_name')->order('level');
        $m->getList(1);

        $m->output();

    }

    function admin_income($trip_id = 0){

        View::addData(['getList'=>'admin_admin_income?trip_id='.$trip_id]);
        View::hamlReader('home/list','Admin');
    }
    function admin_admin_income(AdminIncomeModel $model,$page = 1,$limit = 50,$trip_id = 0){
        
        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(113);

        # 允许操作接口

        # 设置名字
        $m->setName('代驾订单');
        

        # 设置表头
        $m->setHead('代理');
        $m->setHead('收入');
        $m->setHead('收益');


        # 设置表体
        $m->setBody('user_name');
        $m->setBody('money');
        $m->setBody('profit');


        # 筛选
        $m->where = [];
        $m->where['trip_id'] = $trip_id;

        # 获取列表
        $model->select('*','admin.name>user_name')->order('level');
        $m->getList(1);

        $m->output();

    }


    # 管理出租车
    function admin_taxi(OrderTaxiModel $model,TripModel $tripModel,$page = 1,$limit = 10,$search,$status = -1){
        
        $this->L->adminPermissionCheck(114);

        $name = '出租车订单';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_taxi_get',
                'view'  => 'home/upd',
                'del'   => '../order/admin_taxi_del',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                    [
                        'title'=>'状态',
                        'name'=>'status',
                        'type'=>'select',
                        'option'=>[
                            '-1'=>'请选择',
                            
                        ],'default'=>'-1'
                    ],
                ]
            ];
        
        $opt['req'][1]['option'];

        $statusArr = StatusModel::copyMutiInstance()->get_field('msg','id')->toArray();

        $opt['req'][1]['option'] = $statusArr;
        $opt['req'][1]['option']['-1'] = '请选择';

        # 头部标题设置
        $thead = 
            [

                'ID',
                '用户',
                '司机',
                '状态',
                '起点',
                '终点',
                '预估价(元)',
                '总价(元)',
                '打表',
                '线路'

            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'user_name',
                'driver_name',
                'status_name',
                'start_name',
                'end_name',
                'estimated_price',
                'total_fee',
                [
                    'name'=>'meter',
                    'type'=>'checkbox',
                    'disabled'=>true
                ],
                ['name'=>'route','href'=>true]

            ];
            

        # 列表内容
        $where = [];
        if($status != -1)$where['statuss'] = $status;

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%c)','city_id', explode(',', $this->L->userInfo->city_id)];
        }
        
        if($search){
            $where['search'] = ['start_name LIKE %n OR end_name LIKE %n OR user.name LIKE %n OR driver.name LIKE %n OR user.phone LIKE %n OR driver.phone LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','user.name>user_name','driver.name>driver_name')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = $statusArr[$v->statuss];

            $trip = $tripModel->where(['id'=>$v->id,'type'=>2])->find();

            $v->route = '查看';
            $v->route_href = 'order/map?trip_id='.$trip->trip_id;

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
    function admin_taxi_get(OrderTaxiModel $model,$id){



        $type = 2;
        

        $m = Gets::getSingleInstance($model,$id);

        # 权限
        $m->checkPermission(114);

        # 允许操作接口
        $m->setOpt('get','../order/admin_taxi_get');
        $m->setOpt('back','order/taxi');
        $m->setOpt('upd','../order/admin_taxi_upd');
        $m->setOpt('view','home/upd');

        # 设置表体
        $m->setBody(['type'  =>  'hidden','name'  =>  'id']);
        $m->setBody(['type'  =>  'hidden','name'  =>  'trip_id']);
        $staC = $m->setBody(['title'=>'状态','name'=>'statuss','type'=>'select','default'=>'0','description'=>'<font style="color:red">（一般情况下，不能修改）</font>']);
        
        $m->setBody(['title' =>  '联系人电话（代叫限定）','name'  =>  'phone','size'  =>  '2',]);
        $m->setBody(['title' =>  '联系人称呼（代叫限定）','name'  =>  'name','size'  =>  '2',]);
        $m->setBody(['title' =>  '用户','name'  =>  'user_name','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '用户手机号','name'  =>  'user_phone','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '司机','name'  =>  'driver_name','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '司机手机号','name'  =>  'driver_phone','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '开始地址名字','name'  =>  'start_name','size'  =>  '4','disabled'=>true]);
        $m->setBody(['title' =>  '结束地址名字','name'  =>  'end_name','size'  =>  '4','disabled'=>true]);
        $m->setBody(['title' =>  '实时里程(公里)','name'  =>  'real_distance','size'  =>  '4','disabled'=>true]);
        $m->setBody(['title' =>  '预估价(元)','name'  =>  'estimated_price','size'  =>  '4']);
        $m->setBody(['title' =>  '服务开始时间','name'  =>  'start_time_date','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '服务结束时间','name'  =>  'end_time_date','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '城市','name'  =>  'city_name','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '起步价(元)','name'  =>  'start_fee','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '里程价格(元)','name'  =>  'way_fee','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '等待费用(元)','name'  =>  'lay_fee','size'  =>  '2','disabled'=>true]);
        $m->setBody(['title' =>  '其他费用(元)','name'  =>  'other_fee_content','size'  =>  '4','type'  =>  'textarea','disabled'=>true]);
        $m->setBody(['title' =>  '总价格(元)','name'  =>  'total_fee','size'  =>  '2']);
        $m->setBody(['title' =>  '打表','name'  =>  'meter_1','size'  =>  '2','disabled'=>true]);
        

        # 设置名字
        $m->setName();

        
        $m->getInfo();


        $statusArr = StatusModel::copyMutiInstance()->get_field('msg','id')->toArray();
        $m->tbody[$staC]['option'] = $statusArr;

        if($m->info){
            $trip = TripModel::copyMutiInstance()->select('*','cancelType.name>cancel_type_name')->where(['id'=>$id,'type'=>$type])->find();
        }

        # 司机和用户名字如果有的话
        if($m->info->user_id){
            $m->info->user_name = UserModel::copyMutiInstance()->find($m->info->user_id)->name;
            $m->info->user_phone = UserModel::copyMutiInstance()->find($m->info->user_id)->phone;
        }
        if($m->info->driver_id){
            $m->info->driver_name = DriverModel::copyMutiInstance()->find($m->info->driver_id)->name;
            $m->info->driver_phone = DriverModel::copyMutiInstance()->find($m->info->driver_id)->phone;
        }
        # 服务开始时间、服务结束时间、起步费、真实行程距离、行程id、行程费用
        $m->info->start_time_date = $trip->in_time ? date('Y-m-d H:i:s',$trip->in_time) : '';
        $m->info->end_time_date = $trip->out_time ? date('Y-m-d H:i:s',$trip->out_time) : '';
        $m->info->start_fee = $trip->start_fee;
        $m->info->real_distance = $trip->real_distance / 1000;
        $m->info->trip_id = $trip->trip_id;
        $m->info->way_fee = $m->info->fee != 0?number_format($m->info->fee - $m->info->start_fee,2,'.',''):'0.00';


        # 显示其他费用
        $contents = json_decode($trip->other_fee);
        if($contents){
            foreach($contents as $content)
            $m->info->other_fee_content .= $content->type .'：￥'. $content->price."\n";
        }

        $city = AreaModel::copyMutiInstance()->find($m->info->city_id);
        if($city){
            $province = AreaModel::copyMutiInstance()->find($city->parent_id);
        }
        if(!$city || !$province){
            $m->info->city_name = '';
        }else{
            $m->info->city_name = $province->areaName. ' ' .$city->areaName;
        }

        $m->info->meter_1 = $m->info->meter?'是':'否';

        $m->info->cancelReason = $trip->cancel_type_name.' '.$trip->cancel_reason;

        if($m->info->statuss == 0){
            $m->setBody(['title' =>  '取消原因','name'  =>  'cancelReason','type'  =>  'textarea','disabled'=>true]);
        }

        # 输出
        $m->output();



    }
    function admin_taxi_upd(OrderTaxiModel $model,$id,$toDriver,DriverModel $driverModel,$statuss,TripModel $tripModel){
        $this->L->adminPermissionCheck(114);
        
        $app = $model->find($id);
        !$app && AJAX::error('订单不存在');

        $trip = $tripModel->where(['type'=>2,'id'=>$id])->find();
        !$trip && AJAX::error('行程不存在');

        DB::start();
        
        // Func::push_driver($toDriver,'平台已指定派单，请接乘客。',['type'=>'driving']);
        $data = Request::getSingleInstance()->request($model->field);
        $upd = AdminFunc::upd($model,$id,$data);

        if($statuss != $app->statuss){
            $app->statuss = $statuss;
            $app->save();
            $trip->statuss = $statuss;
            $trip->save();
        }
        
        if($toDriver){
            $driver = $driverModel->find($toDriver);
            !$driver && AJAX::error('司机不存在');
            

            if(!in_array($statuss,[5,10])){
                AJAX::error('该订单无法派单');
            }

            

            $app->driver_id = $driver->id;
            $app->statuss = 20;
            $app->save();
            $trip->driver_id = $driver->id;
            $trip->statuss = 20;
            $trip->save();

            
        }

        

        

        DB::commit();

        $out['upd'] = 1;
        AJAX::success($out);
    }
    function admin_taxi_del(OrderTaxiModel $model,$id){
        $this->L->adminPermissionCheck(114);
        $del = AdminFunc::del($model,$id);
        TripModel::copyMutiInstance()->where(['id'=>$id,'type'=>2])->remove();
        $out['del'] = $del;
        AJAX::success($out);
    }


    # 管理顺风车
    function admin_way(OrderWayModel $model,TripModel $tripModel ,$page = 1,$limit = 10,$search,$status = -1){
        
        $this->L->adminPermissionCheck(115);

        $name = '顺风车订单';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_way_get',
                'view'  => 'home/upd',
                'del'   => '../order/admin_way_del',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                    [
                        'title'=>'状态',
                        'name'=>'status',
                        'type'=>'select',
                        'option'=>[
                            '-1'=>'请选择',
                            
                        ],'default'=>'-1'
                    ],
                ]
            ];
        
        $opt['req'][1]['option'];

        $statusArr = StatusModel::copyMutiInstance()->get_field('msg','id')->toArray();

        $opt['req'][1]['option'] = $statusArr;
        $opt['req'][1]['option']['-1'] = '请选择';

        # 头部标题设置
        $thead = 
            [

                'ID',
                '行程ID',
                '用户',
                '司机',
                '状态',
                '起点',
                '终点',
                '预估价(元)',
                '总价(元)',
                '线路'

            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'trip_id',
                'user_name',
                'driver_name',
                'status_name',
                'start_name',
                'end_name',
                'estimated_price',
                'total_fee',
                ['name'=>'route','href'=>true]

            ];
            

        # 列表内容
        $where = [];
        if($status != -1)$where['statuss'] = $status;

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%c)','city_id', explode(',', $this->L->userInfo->city_id)];
        }
        
        if($search){
            $where['search'] = ['start_name LIKE %n OR end_name LIKE %n OR user.name LIKE %n OR driver.name LIKE %n OR user.phone LIKE %n OR driver.phone LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','user.name>user_name','driver.name>driver_name')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = $statusArr[$v->statuss];

            $trip = $tripModel->where(['id'=>$v->id,'type'=>3])->find();
            $v->trip_id = $trip->trip_id;
            $v->route = '查看';
            $v->route_href = 'order/map?trip_id='.$trip->trip_id;

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
    function admin_way_get(OrderWayModel $model,$id){

        $this->L->adminPermissionCheck(115);
        $name = '';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_way_get',
                'back'  => 'order/way',
                'view'  => 'home/upd',
                'upd'   => '../order/admin_way_upd',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                
                [
                    'title' =>  '开始维度',
                    'name'  =>  'start_latitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '开始经度',
                    'name'  =>  'start_longitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '开始地址名字',
                    'name'  =>  'start_name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束维度',
                    'name'  =>  'end_latitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束经度',
                    'name'  =>  'end_longitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束地址名字',
                    'name'  =>  'end_name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>'派单',
                    'name'  =>'toDriver',
                    'size'  =>'4',
                    'suggest'=>'/admin/staff/admin_suser',
                    'fields'=>[
                        'id'=>'id',
                        'name'=>'名字',
                        'brand'=>'品牌',
                        'car_number'=>'车牌号',
                        'dis'=>'距离'
                    ]
                ]
                
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');

        


        $info = AdminFunc::get($model,$id);

        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;

        if($info->status != 1){
            $tbody[7]['disabled'] = true;
            $tbody[7]['suggest'] = '';
        }else{
            $tbody[7]['suggest'] = '/admin/staff/admin_suser?latitude='.$info->start_latitude.'&longitude='.$info->start_longitude.'&search=';
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
    function admin_way_upd(OrderWayModel $model,$id,$toDriver,UserModel $driverModel,TripModel $tripModel){
        $this->L->adminPermissionCheck(115);
        
        $driver = $driverModel->find($toDriver);
        !$driver && AJAX::error('司机不存在');
        $app = $model->find($id);
        !$app && AJAX::error('订单不存在');

        $trip = $tripModel->where(['type'=>3,'id'=>$id])->find();

        DB::start();

        $app->driver_id = $driver->id;
        $app->status = 2;
        $app->save();
        $trip->driver_id = $driver->id;
        $trip->status = 2;
        $trip->save();

        DB::commit();

        // Func::push($toDriver,'平台已指定派单，请接乘客。',['type'=>'way']);

        $out['upd'] = 1;
        AJAX::success($out);
    }
    function admin_way_del(OrderWayModel $model,$id){
        $this->L->adminPermissionCheck(115);
        $del = AdminFunc::del($model,$id);
        TripModel::copyMutiInstance()->where(['id'=>$id,'type'=>3])->remove();
        $out['del'] = $del;
        AJAX::success($out);
    }


    # 顺风车申请
    function admin_apply(UserApplyModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(116);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_apply_get',
                'view'  => 'home/upd',
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

                '用户ID',
                '名字',
                '手机号',
                '品牌',
                '车牌',
                '驾照',
                '行驶证',
                '城市',
                '状态',
                '申请时间'
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'phone',
                'brand',
                'car_number',
                [
                    'type'=>'pic',
                    'name'=>'driving_license',
                    'href'=>true
                ],
                [
                    'type'=>'pic',
                    'name'=>'driving_permit',
                    'href'=>true
                ],
                'city',
                'status_name',
                'date',

            ];
            

        # 列表内容
        $where = [];

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%c)','city_id', explode(',', $this->L->userInfo->city_id)];
        }
        
        if($search){
            $where['search'] = ['user.name LIKE %n OR user.phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','user.name','user.phone','city.areaName>city')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = [
                '0'=>'申请中',
                '1'=>'审核通过',
                '-1'=>'审核失败'
            ][$v->status];
            $v->driving_license = Func::fullPicAddr($v->driving_license);
            $v->driving_permit = Func::fullPicAddr($v->driving_permit);
            $v->date = date('Y-m-d H:i',$v->craete_time);
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
    
    function admin_apply_get(UserApplyModel $model,$id){
        
        $this->L->adminPermissionCheck(116);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../order/admin_apply_get',
            'upd'   => '../order/admin_apply_upd',
            'back'  => 'order/apply',
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
        
        



    function admin_apply_upd(UserApplyModel $model,$id,$status){
        $this->L->adminPermissionCheck(116);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $app = $model->find($id);
        !$app && AJAX::error('error');

        if($status == 1){
            
            UserModel::copyMutiInstance()->set(['type'=>1,'city_id'=>$app->city_id,'car_number'=>$app->car_number,'brand'=>$app->brand])->save($id);
        }else{
            UserModel::copyMutiInstance()->set(['type'=>0,'city_id'=>$app->city_id])->save($id);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }




    function admin_driver_apply(DriverApplyModel $model,$page = 1,$limit = 10,$search,$type){
        
        $this->L->adminPermissionCheck(146);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_driver_apply_get',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'申请类型',
                        'name'=>'type',
                        'type'=>'select',
                        'option'=>[
                            '0'=>'请选择',
                            '1'=>'代驾',
                            '2'=>'出租车'
                        ],'default'=>'0',
                        'size'=>2
                    ],
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

                '用户ID',
                '名字',
                '手机号',
                '品牌',
                '车牌',
                '驾照',
                '行驶证/身份证',
                '城市',
                '类型',
                '状态',
                '申请时间'
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'phone',
                'brand',
                'car_number',
                [
                    'type'=>'pic',
                    'name'=>'driving_license',
                    'href'=>true
                ],
                [
                    'type'=>'pic',
                    'name'=>'driving_permit',
                    'href'=>true
                ],
                'city',
                'type_name',
                'status_name',
                'date',

            ];
            

        # 列表内容
        $where = [];

        if($type){
            $where['type'] = $type;
        }

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%c)','city_id', explode(',', $this->L->userInfo->city_id)];
        }
        
        if($search){
            $where['search'] = ['driver.name LIKE %n OR driver.phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','driver.phone','driver.name','city.areaName>city')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = [
                '0'=>'申请中',
                '1'=>'审核通过',
                '-1'=>'审核失败'
            ][$v->status];
            $v->driving_license = Func::fullPicAddr($v->driving_license);
            $v->driving_permit = Func::fullPicAddr($v->driving_permit);
            $v->date = date('Y-m-d H:i',$v->create_time);
            $v->type_name =[
                            '0'=>'无',
                            '1'=>'代驾',
                            '2'=>'出租车'
                        ][$v->type];
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
    
    function admin_driver_apply_get(DriverApplyModel $model,$id){
        
        $this->L->adminPermissionCheck(146);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../order/admin_driver_apply_get',
            'upd'   => '../order/admin_driver_apply_upd',
            'back'  => 'order/driver_apply',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [

                'name'  =>  'type',
                'title' =>  '类型',
                'type'  =>  'select',
                'option'=>[
                    '1'=>'代驾',
                    '2'=>'出租车'
                ],
                'disabled'=>true,
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

            
            $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
            AJAX::success($out);
            
        }
        
        



    function admin_driver_apply_upd(DriverApplyModel $model,$id,$status,$type){
        $this->L->adminPermissionCheck(146);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $app = $model->find($id);
        !$app && AJAX::error('error');
        $type = $app->type;

        if($status == 1){
            if($type==1){
                $data2['type_driving'] = 1;
                $data2['type_taxi'] = 0;
            }elseif($type==2){
                $data2['type_taxi'] = 1;
                $data2['type_driving'] = 0;
            }

            $data2['city_id'] = $app->city_id;
            $data2['car_number'] = $app->car_number;
            $data2['brand'] = $app->brand;

            
            DriverModel::copyMutiInstance()->set($data2)->save($id);
        }else{
            DriverModel::copyMutiInstance()->set(['type_taxi'=>0,'type_driving'=>0,'city_id'=>$app->city_id])->save($id);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    

    function map($trip_id = 0){

        

        $data['key'] = $this->L->config->GAODE_KEY;
        $data['trip_id'] = $trip_id;

        View::addData($data);
        View::hamlReader('order/map','Admin');

    }


    function mapInfo($trip_id = 0,TripModel $tripModel,DriverOnlineModel $driverOnlineModel,DriverServingPositionModel $driverServingPosition){

        
        $info = AdminFunc::get($tripModel,$trip_id);

        if($info->statuss == 30 && $info->driver_id){
            $info->driverPosition = $driverOnlineModel->find($info->driver_id);
        }

        $info->lines = $driverServingPosition->where(['trip_id'=>$trip_id,'status'=>30])->order('id')->get()->toArray();

        

        $out['info'] = $info;

        AJAX::success($out);

    }

    # 400派单
    function admin_400_get(OrderDrivingModel $model,AreaModel $areaModel,$id){

        $m = Gets::getSingleInstance($model,0);

        # 权限
        $m->checkPermission(165);

        # 允许操作接口
        $m->setOpt('get','../order/admin_400_get');

        $areas = $areaModel->where('level=1 OR (level=0 AND seq=1)')->order('pinyin')->get_field('areaName');
        $areas['0'] = '请选择';

        # 设置表体
        $m->setBody(['type'=>'hidden','name'=>'id']);
        $m->setBody(['title'=>'发起人（手机号）','name'=>'sphone','size'=>'4']);
        $m->setBody(['type'  =>  'select','option'=>$areas,'title'=>'起点选择','name'=>'province_id1','default'=>'0']);
        $staD = $m->setBody(['title' =>'起点地址','name'  =>'start','size'  =>'4','fields'=>['name'=>'名字','cityname'=>'城市','address'=>'详细地址','location'=>'经纬度','id'=>'id'],'add_data'=>['location'],'fnPreprocessKeyword'=>'startFnPreprocessKeyword']);
        $m->setBody(['type'  =>  'select','option'=>$areas,'title'=>'终点选择','name'=>'province_id2','default'=>'0']);
        $staD2 = $m->setBody(['title' =>'终点地址','name'  =>'end','size'  =>'4','fields'=>['name'=>'名字','cityname'=>'城市','address'=>'详细地址','location'=>'经纬度','id'=>'id'],'fnPreprocessKeyword'=>'endFnPreprocessKeyword','add_data'=>['location']]);
        $m->setBody(['title'=>'代叫电话','name'=>'phone','size'=>'4']);
        $m->setBody(['title'=>'代叫人','name'=>'name','size'=>'4']);
        
        
        

        $m->tbody[$staD]['suggest'] = '/home/searchGeo?address=';
        $m->tbody[$staD2]['suggest'] = '/home/searchGeo?address=';

        # 设置名字
        $m->setName('400派单');
        
        # 输出
        $out = [
            'info'  =>  $m->info,
            'tbody' =>  $m->tbody,
            'name'  =>  $m->name,
            'opt'   =>  $m->opt,
        ];
        
        AJAX::success($out);
    }

}
    