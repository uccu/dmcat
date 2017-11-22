<?php

namespace App\Car\Controller;

use Controller;
use Uccu\DmcatTool\Tool\AJAX;
use Response;
use View;
use Request;
use stdClass;
use App\Car\Tool\Func;
use App\Car\Middleware\L;


use App\Car\Model\AreaModel;
use App\Car\Model\OrderDrivingModel;
use App\Car\Model\OrderTaxiModel;
use App\Car\Model\OrderWayModel;
use App\Car\Model\TripModel;
use App\Car\Model\PaymentModel;
use App\Car\Model\UserModel;
use App\Car\Model\DriverModel;

class PayController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();

    }
    function alipay($trip_id,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel,TripModel $tripModel){


        //登陆验证
        !$this->L->id && AJAX::error('请登录');

        $trip = $tripModel->find($trip_id);
        !$trip && AJAX::error('行程不存在');
        $trip->status->status != 4 && AJAX::error('无法支付该订单');

        if($trip->type == 1){
            $order = $orderDrivingModel->find($trip->id);
            $type = 'A';
        }elseif($trip->type == 2){
            $order = $orderTaxiModel->find($trip->id);
            $type = 'B';
        }elseif($trip->type == 3){
            $order = $orderWayModel->find($trip->id);
            $type = 'C';
        }else{
            AJAX::error('未知的订单类型');
        }

        !$order && AJAX::error('订单不存在');

        /*总价格&订单号*/
        // $total_fee = $order->total_price;
        $total_fee = '0.01';
        $out_trade_no = $type.date('YmdHis',$order->create_time).Func::add_zero($order->id,6);

        /*生成随机码*/
        $nonce_str = Func::randWord(32,2);



        $p['partner']           = $this->L->config->aliay_partner;          // 签约的支付宝账号对应的支付宝唯一用户号
        $p['seller_id']         = $this->L->config->aliay_seller_id;        // 签约卖家支付宝账号
        $p['out_trade_no']      = $out_trade_no;                            // 商户网站唯一订单号
        $p['subject']           = '代驾费用';                                // 商品名称
        $p['body']              = '代驾费用';                                // 商品详情
        $p['total_fee']         = $total_fee;                               // 商品金额
        $p['notify_url']        = Func::fullAddr('pay/alipay_c');           // 服务器异步通知页面路径
        $p['service']           = 'mobile.securitypay.pay';                 // 服务接口名称， 固定值
        $p['payment_type']      = '1';                                      // 支付类型， 固定值
        $p['_input_charset']    = 'utf-8';                                  // 参数编码， 固定值
        $p['it_b_pay']          = '30m';                                    // 设置未付款交易的超时时间


        foreach($p as $k=>$v)$info[] = $k.'="'.$v.'"';
        $info = implode('&',$info);

        $res = openssl_get_privatekey ( $this->L->config->alipay_rsa_private_key );
        openssl_sign ( $info, $sign, $res );
        openssl_free_key ( $res );
        // base64编码
        $sign = base64_encode ( $sign );
        $sign = urlencode ( $sign );
        // 执行签名函数
        $info .= "&sign=\"" . $sign . "\"&sign_type=\"RSA\"";

        
        $data['user_id'] = $this->L->id;
        $data['ctime'] = TIME_NOW;
        $data['nonce_str'] = $nonce_str;
        $data['pay_type'] = 'alipay';
        $data['total_fee'] = $total_fee;
        $data['out_trade_no'] = $out_trade_no;
        $data['trip'] = $trip_id;


        
        $id = PaymentModel::getSingleInstance()->set($data)->add()->getStatus();
        if(!$id)AJAX::success('支付单生成失败');

        $data['param'] = $info;

        AJAX::success($data);
    }


    /** 支付宝回调
     * alipay_c
     * @param mixed $out_trade_no 
     * @param mixed $trade_status 
     * @return mixed 
     */
    function alipay_c($out_trade_no,$trade_status,PaymentModel $paymentModel,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel,TripModel $tripModel){

        $out_trade_no = 'C201710101212000004';
        $type = substr($out_trade_no,0,1);
        $order_id = substr($out_trade_no,-6);
        // echo $type.$order_id;die("\n");
        if($type == 'A'){
            $order = $orderDrivingModel->find($order_id);
            $trip = $tripModel->where(['type'=>1,'id'=>$order_id])->find();
        }elseif($type == 'B'){
            $order = $orderTaxiModel->find($trip->id);
            $trip = $tripModel->where(['type'=>1,'id'=>$order_id])->find();
        }elseif($type == 'C'){
            $order = $orderWayModel->find($trip->id);
            $trip = $tripModel->where(['type'=>1,'id'=>$order_id])->find();
        }else{
            echo 'fail';die();
        }

        !$trip && AJAX::error('行程不存在');
        $trip->status->status != 4 && AJAX::error('无法支付该订单');
        !$order && AJAX::error('订单不存在');


        $alipay_partner     = $this->L->config->aliay_partner;
        $alipay_account     = $this->L->config->aliay_seller_id;
        $alipay_private_key = $this->L->config->alipay_rsa_private_key;
        $alipay_public_key  = $this->L->config->alipay_rsa_public_key;

        $data = Request::getInstance()->request;

        foreach($data as &$v){
            $v = (string)$v;
        }

        if($trade_status == 'WAIT_BUYER_PAY'){

            echo 'success';die();
        }

        if($trade_status != 'TRADE_SUCCESS'){

            AJAX::error('支付失败！');
        }

        # 验证签名
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['sign_type']);
        ksort($data);

        $ob = [];
        $str = '';
        foreach($data as $k=>$v){

            $ob[] = $k.'='.$v;
        }

        $str = implode('&',$ob);        
        $res = openssl_get_publickey ( $alipay_public_key );
        $sign = base64_decode($sign);
        $result = (bool)openssl_verify($str,$sign,$res);
        openssl_free_key($res);

        if(!$result){
            AJAX::error('签名验证失败！');
        }
        
        $payLog = $paymentModel->where(['out_trade_no'=>$out_trade_no])->find();
        !$payLog && AJAX::error('没有订单！');


        if($payLog->success_time != 0){

            AJAX::error('支付单已支付！');
        }

        


        $this->pay_finish($trip,$order);


        $payLog->success_time = TIME_NOW;
        $payLog->update_time = TIME_NOW;
        $payLog->open_order_id = $data['trade_no'] ? $data['trade_no'] : '';
        $payLog->open_id = $data['buyer_id'] ? $data['buyer_id'] : '';
        $payLog->name = $data['buyer_login_id'] ? $data['buyer_login_id'] : '';
        $payLog->account = $data['buyer_email'] ? $data['buyer_email'] : '';
        $payLog->pay_nonce_str = $data['notify_id'] ? $data['notify_id'] : '';
        $payLog->success_date = date('Y-m-d',TIME_NOW);

        $payLog->save();

        echo 'success';
    }


    private function pay_finish($trip,$order){

        $trip->status = 5;
        $trip->save();

        $order->status = 5;
        $order->save();

        # 增加收入
        Func::addIncome($trip->driver_id,$trip->user_id,$order->total_fee,$trip->type,$trip->id);

    }
    

}