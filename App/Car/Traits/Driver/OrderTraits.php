<?php

namespace App\Car\Traits\Driver;

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
use App\Car\Model\DriverCancelReasonModel;
use App\Car\Model\UserModel;
use App\Car\Model\DriverModel;
use App\Car\Model\JudgeDriverModel;
use App\Car\Model\OrderTaxiModel;
use App\Car\Model\OrderWayModel;
use App\Car\Model\TripDrivingLogModel;

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
        OrderDrivingModel $orderDrivingModel,TripModel $tripModel){
        
        // $this->L->id = 1;
        !$this->L->id && AJAX::error('未登录');

        !$id && AJAX::error('订单参数缺失');

        $order = $orderDrivingModel->where(['id'=>$id,'driver_id'=>$this->L->id])->find();
        !$order && AJAX::error('订单不存在');

        $trip = $tripModel->select('*','cancelType.name>cancel_type_name')->where(['id'=>$id,'type'=>1,'driver_id'=>$this->L->id])->find();
        !$trip && AJAX::error('订单不存在');

        $user = UserModel::copyMutiInstance()->select('id')->find($trip->user_id);
        if(!$user)AJAX::error('用户不存在');

        $user->online = '0';
        
        $userOnline = UserOnline::copyMutiInstance()->find($trip->user_id);
        if($userOnline && $driverPosition->latitude != 0){
            $user->position = $userOnline;
            $user->online = '1';
        }

        $order->userInfo = $user;

        $startMsg = Func::getArea($order->start_latitude,$order->start_longitude);
        $order->start_formatted_address = $startMsg->formatted_address;
        $startMsg = Func::getArea($order->end_latitude,$order->end_longitude);
        $order->end_formatted_address = $startMsg->formatted_address;


        # 当已抢单，没有接到乘客开始服务，计算司机与起点的距离
        $driverPosition = Func::getDriverPostion($this->L->id);
        if(!$driverPosition || $driverPosition->latitude || $order->statuss == 20){
            $distance = 0;
        }else{
            $distanceObj = Func::getDistance($order->start_latitude, $order->start_longitude, $driverPosition->latitude, $driverPosition->longitude,3);
            $distance = $distanceObj->distance;
            $duration = $distanceObj->duration;
        }
        if($distance < 1000)$start_distance = $distance.'m/'.Func::time_zcalculate($duration) ;
        else $start_distance = number_format( $distance/1000,1,'.','').'km/'.Func::time_zcalculate($duration);

        



        # 司机到起点的距离
        $order->start_distance = $start_distance;
        # 实时行程距离
        $order->real_distance = $trip->real_distance;
        // if($order->real_distance < 1000)$start_distance = $order->real_distance.'米';
        // else 
        $order->real_distance = number_format( $order->real_distance/1000,1,'.','').'公里';

        # 预估价
        $order->estimated_price;

        # 实时价格
        $order->fee;

        # 开始服务时间
        $trip->in_time;

        # 起步费
        $order->start_fee = $trip->start_fee;

        # 当正在服务中，实时计算价格
        if(in_array($order->statuss,[30])){
            $time = date('H:i',$trip->in_time);
            $data = Func::getDrivingPrice($order->city_id,$time,$trip->real_distance / 1000);
            $order->fee = $data['total'];
            $order->start_fee = $data['start'];
            $order->total_fee = $order->fee - $order->coupon;
        }
        $order->trip_id = $trip->trip_id;
        $order->other_fee = $trip->other_fee ? json_decode($trip->other_fee):[];
        $order->cancel_type_name = $trip->cancel_type_name;
        $order->cancel_reason = $trip->cancel_reason;
        $order->start_lay_time = $trip->start_lay_time;

        $order->hasLog = TripDrivingLogModel::copyMutiInstance()->find($trip->trip_id) ?'1':'0';


        $out['info'] = $order;

        AJAX::success($out);


    }


    /** 获取失败原因
     * getCancelReason
     * @param mixed $model 
     * @return mixed 
     */
    function getCancelReason(DriverCancelReasonModel $model){

        $out['list'] = $model->get_field('msg')->toArray();
        array_push($out['list'],'其他');
        AJAX::success($out);

    }

    /** 司机评价
     * judge
     * @param mixed $judgeModel 
     * @param mixed $tripModel 
     * @param mixed $id 
     * @param mixed $comment 
     * @param mixed $tag 
     * @param mixed $orderDrivingModel 
     * @param mixed $orderTaxiModel 
     * @param mixed $orderWayModel 
     * @return mixed 
     */
    function judge_driver(UserModel $userModel,DriverModel $driverModel,JudgeDriverModel $judgeModel,TripModel $tripModel,$score,$id,$comment,$tag,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel){

        !$this->L->id && AJAX::error('未登录');

        $trip = $tripModel->find($id);

        !$trip && AJAX::error('行程不存在');
        $trip->type == 3 && AJAX::error('行程不存在');
        $trip->driver_id != $this->L->id && AJAX::error('司机不符'.$trip->driver_id.','.$this->L->id);
        in_array($trip->statuss,[60,66]) && AJAX::error('已评价');

        $obj = new stdClass;

        $score = floor($score);
        if($score>5)$score = 5;

        $obj->driver_id = $trip->driver_id;
        $obj->trip_id = $id;
        $obj->score = $score;
        $obj->user_id = $trip->user_id;
        $obj->type = $trip->type;
        $obj->create_time = TIME_NOW;

        DB::start();

        $judgeModel->set($obj)->add();

        if($trip->statuss == 50)$trip->statuss = 60;
        if($trip->statuss == 60)$trip->statuss = 66;
        $trip->save();

        if($trip->type == 1){

            $orderDrivingModel->set(['statuss'=>$trip->statuss])->save($trip->id);
            
        }elseif($trip->type == 2){

            $orderTaxiModel->set(['status'=>$trip->statuss])->save($trip->id);
            
        }elseif($trip->type == 3){

            $orderWayModel->set(['status'=>$trip->statuss])->save($trip->id);
            
        }

        
        $score = $judgeModel->select('AVG(score) AS c','RAW')->where(['user_id'=>$trip->user_id])->find()->c;
        !$score && $score = 0;
        $userModel->set(['user_judge_score'=>$score])->save($trip->user_id);

        


        DB::commit();

        AJAX::success(['score'=>$score]);



    }


    /** 提交服务记录
     * drivingLog
     * @param mixed $model 
     * @param mixed $trip_id 
     * @param mixed $brade 
     * @param mixed $car_number 
     * @param mixed $sex 
     * @param mixed $type 
     * @param mixed $tripModel 
     * @return mixed 
     */
    function drivingLog(TripDrivingLogModel $model,$trip_id,$brade,$car_number,$sex,$type,TripModel $tripModel){
        
        !$this->L->id && AJAX::error('未登录');

        $trip = $tripModel->find($trip_id);
        !$trip && AJAX::error('行程不存在');
        $this->L->id != $trip->driver_id && AJAX::error('无权限');

        $model->find($trip_id) && AJAX::error('已记录');

        $data['type'] = $type;
        $data['trip_id'] = $trip_id;
        $data['brade'] = $brade;
        $data['car_number'] = $car_number;
        $data['sex'] = $sex;

        $model->set($data)->add(true);

        AJAX::success();


    }
}
