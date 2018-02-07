<?php

namespace App\Car\Controller;

use Controller;
use Uccu\DmcatTool\Tool\AJAX;
use Uccu\DmcatHttp\Response;
use View;
use Uccu\DmcatHttp\Request;
use stdClass;
use App\Car\Tool\Func;
use App\Car\Middleware\L;



use App\Car\Model\OrderModel;
use App\Car\Model\CarNumberModel;
use App\Car\Model\ParkingLotModel;


class OrderController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();

    }
    

    /** 进入停车场
     * enterLot
     * @param mixed $parkingLotId 
     * @param mixed $car_number 
     * @param mixed $orderModel 
     * @param mixed $carNumberModel 
     * @param mixed $parkingLotModel 
     * @return mixed 
     */
    function enterLot($lotId = 0,$number = '',OrderModel $orderModel,CarNumberModel $carNumberModel,ParkingLotModel $parkingLotModel){

        if(!$number){
            echo 'fail.车牌号不能为空';die();
        }

        $car = $carNumberModel->where(['car_number'=>$number])->find();

        if(!$car){

            $car = new stdClass;
            $car->car_number = $number;
            $car->create_time = TIME_NOW;
            $car->user_id = 0;
            $car->id = $carNumberModel->set($car)->add()->getStatus();
        }

        $lot = $parkingLotModel->find($lotId);

        if(!$lot){
            echo 'fail.停车场不存在';die();
        }

        $data['car_number_id'] = $car->id;
        $data['enter_time'] = TIME_NOW;
        $data['parking_lot_id'] = $lotId;
        $data['status'] = 0;

        $order = $orderModel->set($data)->add()->getStatus();

        if(!$order){
            echo 'fail.创建订单失败';die();
        }


        # 如果用户存在，推送信息
        if($car->user_id){
            Func::push($car->user_id,'您的车辆已经进入',['type'=>'enterLot']);
        }

        echo 'success';


    }
    


    /** 进行中的订单
     * duringList
     * @param mixed $page 
     * @param mixed $limit 
     * @param mixed $orderModel 
     * @return mixed 
     */
    function duringList($page = 1,$limit = 10,OrderModel $orderModel){

        !$this->L->id && AJAX::error('未登录');

        $where['car.user_id'] = $this->L->id;
        $where['status'] = ['status>-1 AND is_paid = 0'];

        $list = $orderModel->select('*','parkingLot.name','parkingLot.address')->page($page,$limit)->where($where)->order('enter_time desc')->get()->toArray();

        foreach($list as &$v){
            if(!$v->leave_time)$v->leave_time = TIME_NOW;
            $v->duringObj = Func::duringZcalculate($v->leave_time - $v->enter_time);
            $v->priceObj = Func::priceZcalculate($v->duringObj,$v->parking_lot_id);
        }

        $out['list'] = $list;
        AJAX::success($out);

    }

    /** 历史订单
     * duringList
     * @param mixed $page 
     * @param mixed $limit 
     * @param mixed $orderModel 
     * @return mixed 
     */
    function historyList($page = 1,$limit = 10,OrderModel $orderModel){

        !$this->L->id && AJAX::error('未登录');

        $where['car.user_id'] = $this->L->id;
        $where['is_paid'] = 1;

        $list = $orderModel->select('*','parkingLot.name','parkingLot.address')->page($page,$limit)->where($where)->order('enter_time desc')->get()->toArray();

        foreach($list as &$v){

            $v->duringObj = Func::duringZcalculate($v->leave_time - $v->enter_time);
            $v->priceObj = Func::priceZcalculate($v->duringObj,$v->parking_lot_id);
        }

        $out['list'] = $list;
        AJAX::success($out);

    }

    # 账单申诉
    function feedback($comment,$order_id,OrderFeedbackModel $orderFeedbackModel,OrderModel $orderModel){
        
        !$comment && AJAX::error('申诉内容不能为空');
        if(!$order_id || $order = $orderModel->select('*','car.user_id')->find($order_id)){
            AJAX::error('订单不存在');
        }

        $order->user_id != $this->L->id && AJAX::error('无法操作该订单');

        $data['order_id'] = $order_id;
        $data['create_time'] = TIME_NOW;
        $data['comment'] = $comment;

        $orderFeedbackModel->set($data)->add();

        AJAX::success();

    }
    

}