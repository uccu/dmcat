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
use App\Car\Model\OrderTaxiModel;
use App\Car\Model\OrderWayModel;
use App\Car\Model\TripModel;
use App\Car\Model\DriverModel;
use App\Car\Model\UserModel;
use App\Car\Model\UserCancelReasonModel;
use App\Car\Model\DriverServingPositionModel;
use App\Car\Model\UserOnlineModel;
use App\Car\Model\JudgeDriverModel;

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
    function orderInfo_daijia($id = 0,$type = 1,$trip_id = 0,
        OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel,TripModel $tripModel,DriverModel $driverModel,UserModel $userModel,DriverServingPositionModel $driverServingPosition){
        
        // $this->L->id = 46;
        !$this->L->id && AJAX::error('未登录');

        !$id && !$trip_id && AJAX::error('订单参数缺失');

        if($trip_id){
            $trip = $tripModel->select('*','cancelType.name>cancel_type_name')->where(['trip_id'=>$trip_id,'user_id'=>$this->L->id])->find();
            !$trip && AJAX::error('行程不存在');
            $id = $trip->id;
            $type = $trip->type;
            if($trip->type == 1){
                $order = $orderDrivingModel->where(['id'=>$id,'user_id'=>$this->L->id])->find();
            }elseif ($trip->type == 2){
                $order = $orderTaxiModel->where(['id'=>$id,'user_id'=>$this->L->id])->find();
            }elseif ($trip->type == 3){
                $driverModel = $userModel;
                $order = $orderWayModel->where(['id'=>$id,'user_id'=>$this->L->id])->find();
            }
            !$order && AJAX::error('订单不存在');
        }

        elseif($id){
            if($type == 1){
                $order = $orderDrivingModel->where(['id'=>$id,'user_id'=>$this->L->id])->find();
            }elseif ($type == 2){
                $order = $orderTaxiModel->where(['id'=>$id,'user_id'=>$this->L->id])->find();
            }elseif ($trip->type == 3){
                $driverModel = $userModel;
                $order = $orderWayModel->where(['id'=>$id,'user_id'=>$this->L->id])->find();
            }
            !$order && AJAX::error('订单不存在');
            

            $trip = $tripModel->select('*','cancelType.name>cancel_type_name')->where(['id'=>$id,'type'=>$type,'user_id'=>$this->L->id])->find();
            !$trip && AJAX::error('行程不存在');

            $trip_id = $trip->trip_id;

        }else{
            AJAX::error('error');
        }

        
        

        if($trip->statuss == 20){

            $out['route'] = $driverServingPosition->where(['trip_id'=>$trip->trip_id,'status'=>20])->order('id')->get()->toArray();
        }

        if($trip->statuss > 30){

            $out['route'] = $driverServingPosition->where(['trip_id'=>$trip->trip_id,'status'=>30])->order('id')->where()->get()->toArray();
        }

        if($order->driver_id){

            $driver =  $driverModel->select('id>driver_id','name','avatar','phone','judge_score','car_number','brand')->find($order->driver_id);
            if($driver){

                $driver->orderCount = TripModel::copyMutiInstance()->select('COUNT(*) AS c','RAW')->where('statuss>49')->where('type IN (1,2)')->where(['driver_id'=>$order->driver_id])->find()->c;
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
        $driverPosition = Func::getDriverPostion($order->driver_id,$type);
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
        // if($order->real_distance < 1000)$start_distance = $order->real_distance.'米';
        // else 
        $order->real_distance = number_format( $order->real_distance/1000,2,'.','');

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
            if($trip->type == 1){
                $time = date('H:i',$trip->in_time);
                $data = Func::getDrivingPrice($order->city_id,$time,$trip->real_distance / 1000);
                $order->fee = $data['total'];
                $order->start_fee = $data['start'];
                $order->total_fee = $order->fee - $order->coupon;
            }elseif($trip->type == 2){
                $time = date('H:i',$trip->in_time);
                $data = Func::getTaxiPrice($order->city_id,$time,$trip->real_distance / 1000);
                $order->fee = $data['total'];
                $order->start_fee = $data['start'];
                $order->total_fee = $order->fee - $order->coupon;
            }elseif($trip->type == 3){
                $prices = Func::getWayPrice($distance->distance / 1000,$order->num);
                $price = $prices['price'];
                $out['start_price'] = $prices['start'];
            }
            
        }
        $order->trip_id = $trip->trip_id;
        $order->other_fee = $trip->other_fee ? json_decode($trip->other_fee):[];
        $order->cancel_type_name = $trip->cancel_type_name;
        $order->cancel_reason = $trip->cancel_reason;
        

        $out['info'] = $order;
        $out['type'] = $type;

        AJAX::success($out);


    }


    function orderInfoDriver($id = 0,$type = 1,$trip_id = 0,
        OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel,TripModel $tripModel,DriverModel $driverModel,UserModel $userModel,DriverServingPositionModel $driverServingPosition){
        
        // $this->L->id = 46;
        !$this->L->id && AJAX::error('未登录');

        !$id && !$trip_id && AJAX::error('订单参数缺失');

        if($trip_id){
            $trip = $tripModel->select('*','cancelType.name>cancel_type_name')->where(['trip_id'=>$trip_id,'driver_id'=>$this->L->id,'type'=>3])->find();
            !$trip && AJAX::error('行程不存在');
            $id = $trip->id;
            $type = $trip->type;
            if($trip->type == 1){
                $order = $orderDrivingModel->where(['id'=>$id,'driver_id'=>$this->L->id])->find();
            }elseif ($trip->type == 2){
                $order = $orderTaxiModel->where(['id'=>$id,'driver_id'=>$this->L->id])->find();
            }elseif ($trip->type == 3){
                $driverModel = $userModel;
                $order = $orderWayModel->where(['id'=>$id,'driver_id'=>$this->L->id])->find();
            }
            !$order && AJAX::error('订单不存在');
        }

        elseif($id){
            if($type == 1){
                $order = $orderDrivingModel->where(['id'=>$id,'driver_id'=>$this->L->id])->find();
            }elseif ($type == 2){
                $order = $orderTaxiModel->where(['id'=>$id,'driver_id'=>$this->L->id])->find();
            }elseif ($trip->type == 3){
                $driverModel = $userModel;
                $order = $orderWayModel->where(['id'=>$id,'driver_id'=>$this->L->id])->find();
            }
            !$order && AJAX::error('订单不存在');
            

            $trip = $tripModel->select('*','cancelType.name>cancel_type_name')->where(['id'=>$id,'type'=>$type,'driver_id'=>$this->L->id])->find();
            !$trip && AJAX::error('行程不存在');

            $trip_id = $trip->trip_id;

        }else{
            AJAX::error('error');
        }

        
        

        if($trip->statuss == 20){

            $out['route'] = $driverServingPosition->where(['trip_id'=>$trip->trip_id,'status'=>20])->order('id')->get()->toArray();
        }

        if($trip->statuss > 30){

            $out['route'] = $driverServingPosition->where(['trip_id'=>$trip->trip_id,'status'=>30])->order('id')->where()->get()->toArray();
        }

        if($order->driver_id){

            $driver =  $driverModel->select('id>driver_id','name','avatar','phone','judge_score')->find($order->driver_id);
            if($driver){

                $driver->orderCount = TripModel::copyMutiInstance()->select('COUNT(*) AS c','RAW')->where('statuss>49')->where('type IN (1,2)')->where(['driver_id'=>$order->driver_id])->find()->c;
                $driver->online = '0';
                $out['driverInfo'] = $driver;
            }else{
                $order->driver_id = '0';
            }
            
        }else{

        }


        $user = UserModel::copyMutiInstance()->select('id','phone')->find($trip->user_id);
        if(!$user)AJAX::error('用户不存在');

        $user->online = '0';
        if($order->phone){
            $user->phone = $order->phone;
        }
        
        $userOnline = UserOnlineModel::copyMutiInstance()->find($trip->user_id);
        if($userOnline && $userOnline->latitude != 0){
            $user->position = $userOnline;
            $user->online = '1';
        }

        $order->userInfo = $user;


        $startMsg = Func::getArea($order->start_latitude,$order->start_longitude);
        $order->start_formatted_address = $startMsg->formatted_address;
        $startMsg = Func::getArea($order->end_latitude,$order->end_longitude);
        $order->end_formatted_address = $startMsg->formatted_address;


        # 当已抢单，没有接到乘客开始服务，计算司机与起点的距离
        $driverPosition = Func::getDriverPostion($order->driver_id,$type);
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
        // if($order->real_distance < 1000)$start_distance = $order->real_distance.'米';
        // else 
        $order->real_distance = number_format( $order->real_distance/1000,2,'.','');

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
            if($trip->type == 1){
                $time = date('H:i',$trip->in_time);
                $data = Func::getDrivingPrice($order->city_id,$time,$trip->real_distance / 1000);
                $order->fee = $data['total'];
                $order->start_fee = $data['start'];
                $order->total_fee = $order->fee - $order->coupon;
            }elseif($trip->type == 2){
                $time = date('H:i',$trip->in_time);
                $data = Func::getTaxiPrice($order->city_id,$time,$trip->real_distance / 1000);
                $order->fee = $data['total'];
                $order->start_fee = $data['start'];
                $order->total_fee = $order->fee - $order->coupon;
            }elseif($trip->type == 3){
                $prices = Func::getWayPrice($distance->distance / 1000,$order->num);
                $price = $prices['price'];
                $out['start_price'] = $prices['start'];
            }
            
        }
        $order->trip_id = $trip->trip_id;
        $order->other_fee = $trip->other_fee ? json_decode($trip->other_fee):[];
        $order->cancel_type_name = $trip->cancel_type_name;
        $order->cancel_reason = $trip->cancel_reason;
        $order->during = $trip->during;
        $order->pay_type = $trip->pay_type;
        $order->laying = $trip->laying;
        $order->start_lay_time = $trip->start_lay_time;
        if($trip->laying){
            $order->during = $trip->during + TIME_NOW - $order->start_lay_time;
            if($order->during > 600)$order->lay_fee = ceil(($order->during-600)/60);
        }
        if(in_array($order->statuss,[30])){
            $order->total_fee += $order->lay_fee;
        }

        $out['info'] = $order;
        $out['type'] = $type;

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





    /** 更改终点位置
     * changeEnd
     * @param mixed $id 
     * @param mixed $orderDrivingModel 
     * @param mixed $tripModel 
     * @param mixed $end_latitude 
     * @param mixed $end_longitude 
     * @param mixed $end_name 
     * @return mixed 
     */
    function changeEnd($id,$trip_id,OrderWayModel $orderWayModel,TripModel $tripModel,$end_latitude,$end_longitude,$end_name){

        // $this->L->id = 16;
        !$this->L->id && AJAX::error('未登录');

        (!$id && !$trip_id) && AJAX::error('订单参数缺失');

        if(!$end_latitude || !$end_longitude || !$end_name)AJAX::error('订单参数缺失');

        if($id){

            $order = $orderDrivingModel->where(['id'=>$id,'driver_id'=>$this->L->id])->find();
            $trip = $tripModel->where(['id'=>$id,'type'=>1,'driver_id'=>$this->L->id])->find();
            !$trip && AJAX::error('行程不存在');
        }elseif($trip_id){
            $trip = $tripModel->where(['trip_id'=>$trip_id,'driver_id'=>$this->L->id])->find();
            !$trip && AJAX::error('行程不存在');
            if($trip->type == 3){
                $order = $orderWayModel->where(['id'=>$trip->id,'driver_id'=>$this->L->id])->find();
            }
        }
        !$order && AJAX::error('订单不存在');

        DB::start();
        $trip->end_latitude = $end_latitude;
        $trip->end_longitude = $end_longitude;
        $trip->end_name = $end_name;
        $trip->save();

        $order->end_latitude = $end_latitude;
        $order->end_longitude = $end_longitude;
        $order->end_name = $end_name;
        $order->save();
        DB::commit();

        AJAX::success();

    }


    /** 刷新计费
     * refleshPrice
     * @param mixed $tripModel 
     * @param mixed $orderDrivingModel 
     * @param mixed $orderTaxiModel 
     * @param mixed $trip_id 
     * @return mixed 
     */
    function refleshPrice(TripModel $tripModel,OrderWayModel $orderWayModel,$trip_id = 0){

        !$this->L->id && AJAX::error('未登录');

        $trip = $tripModel->find($trip_id);
        !$trip && AJAX::error('行程不存在');

        if($trip->type == 3){
            $model = $orderWayModel;
        }else{
            AJAX::error('行程不存在M');
        }
        if($trip->statuss != 35){
            AJAX::error('请在确认计费前刷新计费');
        }
        $order = $model->where(['id'=>$trip->id,'driver_id'=>$this->L->id])->find();
        !$order && AJAX::error('订单不存在');


        $distance = Func::getDistance($order->start_latitude,$order->start_longitude,$order->end_latitude,$order->end_longitude,1);
        if(!$distance)AJAX::error('距离获取失败');

        if($trip->type == 3){

            $time = date('H:i',$trip->in_time);
            if(!$time)$time = date('H:i');
            $data = Func::getWayPrice($order->city_id,$time,$distance->distance / 1000);
            $price = $data['price'];
            $start = $data['start'];

        }
        if(!$price)AJAX::error('价格获取失败');

        DB::start();
        $order->fee = $price;
        $order->estimated_price = $price;
        $order->total_fee = $price + $order->lay_fee;
        $order->save();

        $trip->start_fee = $start;
        $trip->save();

        DB::commit();

        AJAX::success();


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
    function judge_driver(UserModel $userModel,DriverModel $driverModel,JudgeDriverModel $judgeModel,TripModel $tripModel,$score,$trip_id,$comment,$tag,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel){

        !$this->L->id && AJAX::error('未登录');

        $trip = $tripModel->find($trip_id);

        !$trip && AJAX::error('行程不存在');
        $trip->type != 3 && AJAX::error('行程不存在');
        $trip->driver_id != $this->L->id && AJAX::error('司机不符'.$trip->driver_id.','.$this->L->id);
        in_array($trip->statuss,[60,66]) && AJAX::error('已评价');

        $obj = new stdClass;

        $score = floor($score);
        if($score>5)$score = 5;

        $obj->driver_id = $trip->driver_id;
        $obj->trip_id = $trip_id;
        $obj->user_id = $trip->user_id;
        $obj->type = $trip->type;

        $judgeModel->where($obj)->find() && AJAX::error('已评价');
        $obj->score = $score;
        $obj->create_time = TIME_NOW;

        DB::start();

        $judgeModel->set($obj)->add();

        if($trip->statuss == 50)$trip->statuss = 60;
        if($trip->statuss == 55)$trip->statuss = 66;
        $trip->save();

        if($trip->type == 1){

            $orderDrivingModel->set(['statuss'=>$trip->statuss])->save($trip->id);
            
        }elseif($trip->type == 2){

            $orderTaxiModel->set(['statuss'=>$trip->statuss])->save($trip->id);
            
        }elseif($trip->type == 3){

            $orderWayModel->set(['statuss'=>$trip->statuss])->save($trip->id);
            
        }

        
        $score = $judgeModel->select('AVG(score) AS c','RAW')->where(['user_id'=>$trip->user_id])->find()->c;
        !$score && $score = 0;
        $userModel->set(['user_judge_score'=>$score])->save($trip->user_id);

        


        DB::commit();

        AJAX::success(['score'=>$score]);



    }

}
