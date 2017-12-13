<?php

namespace App\Car\Traits\User;

use Controller;
use DB;
use stdClass;
use Response;
use Request;
use App\Car\Middleware\L2;
use App\Car\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;


# model
use App\Car\Model\OrderDrivingModel;
use App\Car\Model\TripModel;
use App\Car\Model\DriverModel;
use App\Car\Model\UserCancelReasonModel;

/**
 *  司机订单相关
 */
trait OrderTraits{



    /** 代驾的订单详情
     * orderInfo_daijia
     * @param mixed $id 
     * @param mixed $orderDrivingModel 
     * @return mixed 
     */
    function orderInfo_daijia($id = 0,
        OrderDrivingModel $orderDrivingModel,TripModel $tripModel,DriverModel $driverModel){
        
        // $this->L->id = 46;
        !$this->L->id && AJAX::error('未登录');

        !$id && AJAX::error('订单参数缺失');

        $order = $orderDrivingModel->where(['id'=>$id,'user_id'=>$this->L->id])->find();
        !$order && AJAX::error('订单不存在');

        $trip = $tripModel->where(['id'=>$id,'type'=>1,'user_id'=>$this->L->id])->find();
        !$trip && AJAX::error('订单不存在');

        if($order->driver_id){

            $driver =  $driverModel->select('id>driver_id','name','avatar','phone','judge_score')->find($order->driver_id);
            if($driver){

                $driver->orderCount = TripModel::copyMutiInstance()->select('COUNT(*) AS c','RAW')->where('statuss>49')->where('type<3')->where(['driver_id'=>$order->driver_id])->find()->c;
                $driver->online = '0';
                $out['driverInfo'] = $driver;
            }else{
                $order->driver_id = '0';
            }
            
        }else{

        }


        // $startMsg = Func::getArea($order->start_latitude,$order->start_longitude);
        // $order->start_formatted_address = $startMsg->formatted_address;
        // $startMsg = Func::getArea($order->end_latitude,$order->end_longitude);
        // $order->end_formatted_address = $startMsg->formatted_address;


        # 当已抢单，没有接到乘客开始服务，计算司机与起点的距离
        $driverPosition = Func::getDriverPostion($order->driver_id);
        if(!$driverPosition || $driverPosition->latitude || $order->statuss == 20){
            $distance = 0;
        }else{
            $distanceObj = Func::getDistance($order->start_latitude, $order->start_longitude, $driverPosition->latitude, $driverPosition->longitude,3);
            $distance = $distanceObj->distance;
            $duration = $distanceObj->duration;
        }
        if($distance < 1000)$start_distance = $distance.'m/'.Func::time_zcalculate($duration) ;
        else $start_distance = number_format( $distance/1000,1,'.','').'km/'.Func::time_zcalculate($duration);

        if($order->driver_id && $driverPosition){
            $driver->online = '1';
            $driver->position = $driverPosition;
        }



        # 司机到起点的距离
        $order->start_distance = $start_distance;
        # 实时行程距离
        $order->real_distance = $trip->real_distance;
        if($order->real_distance < 1000)$start_distance = $order->real_distance.'米';
        else $start_distance = number_format( $order->real_distance/1000,1,'.','').'公里';

        # 预估价
        $order->estimated_price;

        # 实时价格
        $order->fee;

        # 开始服务时间
        $trip->in_time;

        # 当正在服务中，实时计算价格
        if(in_array($order->statuss,[30])){
            $time = date('H:i',$trip->in_time);
            $data = Func::getDrivingPrice($order->city_id,$time,$trip->real_distance / 1000);
            $order->fee = $data['total'];
        }
        

        $out['info'] = $order;

        AJAX::success($out);


    }


    /** 获取失败原因
     * getCancelReason
     * @param mixed $model 
     * @return mixed 
     */
    function getCancelReason(UserCancelReasonModel $model){

        $out['list'] = $model->get_field('msg')->toArray();
        array_push($out['list'],'其他');
        AJAX::success($out);

    }

}
