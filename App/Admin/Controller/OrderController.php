<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Uccu\DmcatHttp\Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;
use DB;

# 数据模型
use App\Car\Model\OrderModel;
use App\Car\Model\AreaModel;
use App\Car\Model\OrderFeedbackModel;


use App\Admin\Set\Gets;
use App\Admin\Set\Lists;


class OrderController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'order';
    }


    /*  代驾 */
    function order($id = 0){

        View::addData(['getList'=>'admin_order?id='.$id]);
        View::hamlReader('home/list','Admin');
    }
    
    


    # 管理订单
    function admin_order(AreaModel $areaModel,OrderModel $model,$page = 1,$limit = 50,$user_search,$lot_search,$province_id,$city_id,$district_id,$group_id,$status = -2,$is_paid = -1,$feedback_status = -3,$start_time,$end_time){
        
        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(14);

        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        // $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOptReq(['title'=>'用户名/账号','name'=>'user_search','size'=>'3']);
        $m->setOptReq(['title'=>'停车场','name'=>'lot_search','size'=>'3']);

        $p = $m->setOptReq(['title'=>'省','name'=>'province_id','type'=>'select','default'=>'0','size'=>'2']);
        $p2 = $m->setOptReq(['title'=>'市','name'=>'city_id','type'=>'select','default'=>'0','size'=>'2']);
        $p3 = $m->setOptReq(['title'=>'区','name'=>'district_id','type'=>'select','default'=>'0','size'=>'2']);
        $p4 = $m->setOptReq(['title'=>'商圈','name'=>'group_id','type'=>'select','default'=>'0','size'=>'2']);

        $city_id && $areaModel->find($city_id)->parent_id != $province_id && $city_id = '0';
        $district_id && $areaModel->find($district_id)->parent_id != $city_id && $district_id = '0';
        $group_id && $areaModel->find($group_id)->parent_id != $district_id && $group_id = '0';

        $provinces = $areaModel->select('id','areaName>value')->where('level=0')->order('pinyin')->get()->toArray();
        $citys = $province_id ? $areaModel->select('id','areaName>value')->where('parent_id=%d',$province_id)->order('pinyin')->get()->toArray() : [];
        $districts = $city_id ? $areaModel->select('id','areaName>value')->where('parent_id=%d',$city_id)->order('pinyin')->get()->toArray() : [];
        $groups = $district_id ? $areaModel->select('id','areaName>value')->where('parent_id=%d',$district_id)->order('pinyin')->get()->toArray() : [];

        $m->opt['req'][$p]['option'] = array_merge(['选择省'],$provinces);
        $m->opt['req'][$p2]['option'] = array_merge(['选择市'],$citys);
        $m->opt['req'][$p3]['option'] = array_merge(['选择区'],$districts);
        $m->opt['req'][$p4]['option'] = array_merge(['选择商圈'],$groups);

        $m->setOptReq(['title'=>'订单状态','name'=>'status','type'=>'select','default'=>'-2','size'=>'3','option'=>[
            ['id'=>'-2','value'=>'请选择'],['id'=>'-1','value'=>'取消'],['id'=>'0','value'=>'进行中'],['id'=>'1','value'=>'已离场']
        ]]);
        $m->setOptReq(['title'=>'支付状态','name'=>'is_paid','type'=>'select','default'=>'-1','size'=>'3','option'=>[
            ['id'=>'-1','value'=>'请选择'],['id'=>'0','value'=>'未付款'],['id'=>'1','value'=>'已付款']
        ]]);
        $m->setOptReq(['title'=>'投诉状态','name'=>'feedback_status','type'=>'select','default'=>'-3','size'=>'3','option'=>[
            ['id'=>'-3','value'=>'请选择'],['id'=>'-2','value'=>'未投诉'],['id'=>'0','value'=>'处理中'],['id'=>'1','value'=>'已解决'],['id'=>'-1','value'=>'驳回']
        ]]);

        $m->setOptReq(['title'=>'开始时间','name'=>'start_time','size'=>'3']);
        $m->setOptReq(['title'=>'结束时间','name'=>'end_time','size'=>'3']);

        # 设置名字
        $m->setName('订单');
        

        # 设置表头
        $m->setHead('订单ID');
        $m->setHead('账号');
        $m->setHead('姓名');
        $m->setHead('停车场');
        $m->setHead('省');
        $m->setHead('市');
        $m->setHead('区');
        $m->setHead('商圈');
        $m->setHead('停车时间');
        $m->setHead('时长(时:分)');
        $m->setHead('费用');
        $m->setHead('订单状态');
        $m->setHead('付款状态');
        $m->setHead('投诉状态');
        

        # 设置表体
        $m->setBody('id');
        $m->setBody('phone');
        $m->setBody('userName');
        $m->setBody('lotName');
        $m->setBody('provinceName');
        $m->setBody('cityName');
        $m->setBody('districtName');
        $m->setBody('groupName');
        $m->setBody('enter_date');
        $m->setBody('during');
        $m->setBody('price');
        $m->setBody('status_name');
        $m->setBody(['name'=>'is_paid','type'=>'checkbox','disabled'=>true]);
        $m->setBody('feedback_status');
        

        # 筛选
        if($group_id)$m->where['parkingLot.group_id'] = $group_id;
        elseif($district_id)$m->where['parkingLot.district_id'] = $district_id;
        elseif($city_id)$m->where['parkingLot.district.parent_id'] = $city_id;
        elseif($province_id)$m->where['parkingLot.district.parent.parent_id'] = $province_id;

        if($status != -2){
            $m->where['status'] = $status;
        }

        if($is_paid != -1){
            $m->where['is_paid'] = $is_paid;
        }

        if($feedback_status != -3){
            if($feedback_status == -2){
                $m->where['s'] = ['NOT EXISTS(SELECT `id` FROM `c_order_feedback` WHERE `order_id` = %F)','id'];
            }else $m->where['feedback.status'] = $feedback_status;
        }

        if($start_time && $start = strtotime($start_time)){
            $m->where['start'] = ['enter_time>=%d',$start];
        }
        if($end_time && $end = strtotime($end_time)){
            $m->where['end'] = ['enter_time<%d',$end+24*2600];
        }

        if($user_search){
            $m->where['user'] = ['car.user.name LIKE %n OR car.user.phone LIKE %n','%'.$user_search.'%','%'.$user_search.'%'];
        }
        if($lot_search){
            $m->where['user'] = ['parkingLot.name LIKE %n','%'.$lot_search.'%','%'.$lot_search.'%'];
        }

        
        

        # 获取列表
        $model->select('*','car.user.name>userName','car.user.phone','parkingLot.name>lotName','parkingLot.groups.areaName>groupName','parkingLot.district.areaName>districtName','parkingLot.district.parent.areaName>cityName','parkingLot.district.parent.parent2.areaName>provinceName','feedback.status>feedback_status');
        $m->getList(0);

        $m->fetchArr('status_name','status',['-1'=>'取消','0'=>'进行中','1'=>'已离场']);
        $m->fetchArr('feedback_status','feedback_status',[''=>'未投诉','0'=>'处理中','1'=>'已解决','-1'=>'驳回']);

        $m->each(function($v){
            $v->enter_date = date('Y-m-d H:i',$v->enter_time);
            if(!$v->leave_time)$v->leave_time = TIME_NOW;
            $duringObj = Func::duringZcalculate($v->leave_time - $v->enter_time);
            $priceObj = Func::priceZcalculate($duringObj,$v->parking_lot_id);
            
            $v->during = Func::time_wcalculate($v->leave_time - $v->enter_time);
            if(!$v->price)$v->price = $priceObj->total;

        });

        // echo $model->sql;die();

        $m->output();

    }


    function admin_order_get(OrderModel $model,$id){


        $m = Gets::getSingleInstance($model,$id);

        # 权限
        $m->checkPermission(14);

        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');

        # 设置表体
        $m->setBody(['type'  =>  'hidden','name'  =>  'id']);
        $m->setBody(['title'=>'停车场名称','name'  =>  'lotName','disabled'=>true,'size'=>3]);
        $m->setBody(['title'=>'账号','name'  =>  'phone','disabled'=>true,'size'=>3]);
        $m->setBody(['title'=>'姓名','name'  =>  'userName','disabled'=>true,'size'=>3]);
        $m->setBody(['title'=>'车牌号','name'  =>  'car_number','disabled'=>true,'size'=>3]);
        $m->setBody(['title'=>'省','name'  =>  'provinceName','disabled'=>true,'size'=>2]);
        $m->setBody(['title'=>'市','name'  =>  'cityName','disabled'=>true,'size'=>2]);
        $m->setBody(['title'=>'区','name'  =>  'districtName','disabled'=>true,'size'=>2]);
        $m->setBody(['title'=>'商圈','name'  =>  'groupName','disabled'=>true,'size'=>2]);
        $m->setBody(['title'=>'详细地址','name'  =>  'address','disabled'=>true,'size'=>4]);
        $m->setBody(['title'=>'停车时间','name'  =>  'enter_date','disabled'=>true,'size'=>2]);
        $m->setBody(['title'=>'结束时间','name'  =>  'leave_date','disabled'=>true,'size'=>2]);
        $m->setBody(['title'=>'停车时长(小时:分)','name'  =>  'during_time','disabled'=>true,'size'=>2]);
        $m->setBody(['title'=>'停车费','name'  =>  'price','size'=>2]);
        $m->setBody(['title'  =>  '订单状态','name'  =>  'status','type'=>'radio','option'=>['-1'=>'取消','0'=>'进行中','1'=>'已离场'],'default'=>'0']);
        $m->setBody(['title'  =>  '付款状态','name'  =>  'is_paid','type'=>'radio','option'=>['0'=>'未付款','1'=>'已付款'],'default'=>'0']);
        

        
        $model->select('*','car.user.name>userName','car.user.phone','car.car_number','parkingLot.name>lotName','parkingLot.groups.areaName>groupName','parkingLot.address','parkingLot.district.areaName>districtName','parkingLot.district.parent.areaName>cityName','parkingLot.district.parent.parent2.areaName>provinceName','feedback.status>feedback_status','feedback.comment');

        # 设置名字
        $m->setName();

        $m->getInfo();

        $m->info->during_time = Func::time_wcalculate($m->info->during);
        $m->info->enter_date = date('Y-m-d H:i',$m->info->enter_time);
        $m->info->leave_date = $m->info->leave_time?date('Y-m-d H:i',$m->info->leave_time):'';

        if($m->info->comment){
            $m->setBody(['title'  =>  '投诉内容','name'  =>  'commmet','type'=>'textarea','disabled'=>true]);
            $m->setBody(['title'  =>  '投诉状态','name'  =>  'feedback_status','type'=>'radio','option'=>['0'=>'处理中','1'=>'已解决','-1'=>'驳回'],'default'=>'0']);
            $m->setBody(['title'  =>  '驳回理由','name'  =>  'reason','type'=>'textarea']);
        }
        

        # 输出
        $m->output();

    }


    function admin_order_upd(OrderModel $model,$id,$feedback_status,$reason,OrderFeedbackModel $orderFeedbackModel){
        $this->L->adminPermissionCheck(14);
        
        $app = $model->find($id);
        !$app && AJAX::error('订单不存在');

        DB::start();

        $data = Request::getSingleInstance()->request($model->field);
        $upd = AdminFunc::upd($model,$id,$data);

        if(!is_null($feedback_status)){
            $orderFeedbackModel->set(['status'=>$feedback_status,'reason'=>$reason])->where(['order_id'=>$id])->save();
        }

        DB::commit();

        $out['upd'] = 1;
        $out['field'] = $model->field;
        $out['data'] = $data;
        AJAX::success($out);
    }


    function admin_order_del(OrderModel $model,$id,OrderFeedbackModel $orderFeedbackModel){
        $this->L->adminPermissionCheck(14);
        $del = AdminFunc::del($model,$id);
        $orderFeedbackModel->where(['order_id'=>$id])->remove();
        $out['del'] = $del;
        AJAX::success($out);
    }

    

}
    