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



use App\Car\Model\PaymentModel;


class PayController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();
        $this->L2 = L2::getSingleInstance();

    }
    function alipay($trip_id,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel,TripModel $tripModel){


        //登陆验证
        !$this->L->id && AJAX::error('请登录');

        $trip = $tripModel->find($trip_id);
        !$trip && AJAX::error('行程不存在');
        $trip->statuss != 45 && AJAX::error('无法支付该订单');

        if($trip->type == 1){
            $order = $orderDrivingModel->find($trip->id);
            $type = 'A';
            $name = '代驾';
        }elseif($trip->type == 2){
            $order = $orderTaxiModel->find($trip->id);
            $type = 'B';
            $name = '出租车';
        }elseif($trip->type == 3){
            $order = $orderWayModel->find($trip->id);
            $type = 'C';
            $name = '顺风车';
        }else{
            AJAX::error('未知的订单类型');
        }

        !$order && AJAX::error('订单不存在');

        /*总价格&订单号*/
        $total_fee = $order->total_fee;
        // $total_fee = '0.01';
        $out_trade_no = $type.date('YmdHis',$order->create_time).Func::add_zero($order->id,6);

        /*生成随机码*/
        $nonce_str = Func::randWord(32,2);



        $p['partner']           = $this->L->config->aliay_partner;          // 签约的支付宝账号对应的支付宝唯一用户号
        $p['seller_id']         = $this->L->config->aliay_seller_id;        // 签约卖家支付宝账号
        $p['out_trade_no']      = $out_trade_no;                            // 商户网站唯一订单号
        $p['subject']           = $name.'费用';                                // 商品名称
        $p['body']              = $name.'费用';                                // 商品详情
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
        $data['total_fee'] = $order->total_fee;
        $data['out_trade_no'] = $out_trade_no;
        $data['trip_id'] = $trip_id;


        
        $id = PaymentModel::getSingleInstance()->set($data)->add()->getStatus();
        if(!$id)AJAX::success('支付单生成失败');

        $data['param'] = $info;

        AJAX::success($data);
    }


    





    function wcpay($trip_id,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel,TripModel $tripModel){

        //登陆验证
        !$this->L->id && AJAX::error('请登录');

        $trip = $tripModel->find($trip_id);
        !$trip && AJAX::error('行程不存在');
        $trip->statuss != 45 && AJAX::error('无法支付该订单');

        if($trip->type == 1){
            $order = $orderDrivingModel->find($trip->id);
            $type = 'A';
            $name = '代驾';
        }elseif($trip->type == 2){
            $order = $orderTaxiModel->find($trip->id);
            $type = 'B';
            $name = '出租车';
        }elseif($trip->type == 3){
            $order = $orderWayModel->find($trip->id);
            $type = 'C';
            $name = '顺风车';
        }else{
            AJAX::error('未知的订单类型');
        }

        !$order && AJAX::error('订单不存在');

        /*总价格&订单号*/
        $total_fee = $order->total_fee;
        // $total_fee = '0.01';
        $out_trade_no = $type.date('YmdHis',$order->create_time).Func::add_zero($order->id,6);

        /*生成随机码*/
        $nonce_str = Func::randWord(32,2);
        $nonce_str2 = Func::randWord(32,2);

        $total_fee100 = floor($total_fee*100);
        
        $p['appid']             = $this->L->config->wcpay_appid;
        $p['body']              = $name.'费用';
        $p['mch_id']            = $this->L->config->wcpay_mch_id;
        $p['nonce_str']         = $nonce_str;
        $p['notify_url']        = Func::fullAddr('pay/wcpay_c');
        $p['out_trade_no']      = $out_trade_no;
        $p['spbill_create_ip']  = $_SERVER ["REMOTE_ADDR"];
        $p['total_fee']         = $total_fee100;
        $p['trade_type']        = 'APP';


        $xml = '<xml>';
        $sign = '';
        foreach ( $p as $key => $val ) {
            $sign .= trim ( $key ) . "=" . trim ( $val ) . "&";
            $xml .= "<" . trim ( $key ) . ">" . trim ( $val ) . "</" . trim ( $key ) . ">";
        }
        $sign .= 'key='.$this->L->config->wcpay_key;

        $sign = strtoupper ( md5 ( $sign ) );
        $xml .= "<sign>$sign</sign>";
        $xml .= '</xml>';


        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt ( $ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder" );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $xml );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $da = curl_exec ( $ch );


        
        $data['user_id'] = $this->L->id;
        $data['ctime'] = TIME_NOW;
        $data['nonce_str'] = $nonce_str;
        $data['pay_type'] = 'wcpay';
        $data['total_fee'] = $total_fee;
        $data['out_trade_no'] = $out_trade_no;
        $data['trip_id'] = $trip_id;

        if(!$da){

            $data['error'] = '微信服务器访问超时/无法访问';

            $id = PaymentModel::getSingleInstance()->set($data)->add()->getStatus();
            if(!$id)AJAX::success('支付单生成失败');

            AJAX::error($data['error']);
        }

        $result = simplexml_load_string ( $da );


        if($result->return_code.'' == 'FAIL'){

            $data['error'] = '微信通信失败.'.$result->return_msg;

            $id = PaymentModel::getSingleInstance()->set($data)->add()->getStatus();
            if(!$id)AJAX::success('支付单生成失败');

            AJAX::error($data['error']);
        }
        if($result->result_code.'' == 'FAIL'){

            if($result->err_code == 'ORDERPAID'){


                $this->pay_finish($trip,$order);
                AJAX::error('订单已支付！');

            }

            $data['error'] = '微信预支付交易失败.'.$result->err_code.'.'.$result->err_code_des;

            $id = PaymentModel::getSingleInstance()->set($data)->add()->getStatus();
            if(!$id)AJAX::success('支付单生成失败');

            AJAX::error($data['error']);
        }


        $data['prepay_id'] = $result->prepay_id.'';
        $data['prepay_time'] = TIME_NOW;
        $data['pay_nonce_str'] = $nonce_str2;

        $data2['appid'] = $this->L->config->wcpay_appid;
        $data2['partnerid'] = $this->L->config->wcpay_mch_id;
        $data2['package'] = 'Sign=WXPay';
        $data2['noncestr'] = $nonce_str2;
        $data2['timestamp'] = TIME_NOW.'';
        $data2['prepayid'] = $data['prepay_id'];
        ksort($data2,SORT_STRING);
        foreach ( $data2 as $key => $val ) {
            $signStr .= trim ( $key ) . "=" . trim ( $val ) . "&";
        }
        $signStr .= 'key='.$this->L->config->wcpay_key;

        $data2['sign'] = strtoupper ( md5 ( $signStr ) );
        $data2['prepay_id'] = $data2['prepayid'];
        $id = PaymentModel::getSingleInstance()->set($data)->add()->getStatus();
        if(!$id)AJAX::success('支付单生成失败');

        AJAX::success($data2);

    }

    





    /** 支付宝回调
     * alipay_c
     * @param mixed $out_trade_no 
     * @param mixed $trade_status 
     * @return mixed 
     */
    function alipay_c($out_trade_no,$trade_status,PaymentModel $paymentModel,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel,TripModel $tripModel){

        $type = substr($out_trade_no,0,1);
        $order_id = substr($out_trade_no,-6);
        // echo $type.$order_id;die("\n");
        if($type == 'A'){
            $order = $orderDrivingModel->find($order_id);
            $trip = $tripModel->where(['type'=>1,'id'=>$order_id])->find();
        }elseif($type == 'B'){
            $order = $orderTaxiModel->find($order_id);
            $trip = $tripModel->where(['type'=>2,'id'=>$order_id])->find();
        }elseif($type == 'C'){
            $order = $orderWayModel->find($order_id);
            $trip = $tripModel->where(['type'=>3,'id'=>$order_id])->find();
        }else{
            echo 'fail';die();
        }

        !$trip && AJAX::error('行程不存在');
        if($trip->statuss>45 && $trip->pay_type == 1){
            echo 'success';die();
        }
        $trip->statuss != 45 && AJAX::error('无法支付该订单');
        !$order && AJAX::error('订单不存在');


        $alipay_partner     = $this->L->config->aliay_partner;
        $alipay_account     = $this->L->config->aliay_seller_id;
        $alipay_private_key = $this->L->config->alipay_rsa_private_key;
        $alipay_public_key  = $this->L->config->alipay_rsa_public_key;

        $data = Request::getSingleInstance()->request;

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

    


    /** 微信回调
     * wcpay_c
     * @return mixed 
     */
    function wcpay_c(PaymentModel $paymentModel,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel,TripModel $tripModel){

        $postStr = file_get_contents ( 'php://input' );
        $xmlObject =  simplexml_load_string ( $postStr );
        $xmlArray = [];
        $xmlArray2 = [];
        foreach($xmlObject as $k=>$v){
            $xmlArray[$k] = $xmlArray2[$k] = $v.'';
        }
        $_REQUEST = $xmlArray;
        $sign = $xmlArray['sign'];
        unset($xmlArray['sign']);
        ksort($xmlArray,SORT_STRING);
        if(!$xmlArray || !$xmlArray['result_code'])AJAX::error('微信支付回调失败');
        if($xmlArray['result_code'] != 'SUCCESS')AJAX::error('微信支付回调.'.$xmlArray['return_msg']);

        $signStr = '';

        foreach($xmlArray as $k=>$v){
            $signStr .= $k.'='.$v.'&';
        }
        $signStr .= 'key='.$this->L->config->wcpay_key;

        $model = $paymentModel;
        $out_trade_no = $where['out_trade_no'] = $xmlArray['out_trade_no'];
        $where['nonce_str'] = $xmlArray['nonce_str'];
        $log = $model->where($where)->find();
        if(!$log)AJAX::error('未找到支付单！');
        
        if($sign != strtoupper ( md5 ( $signStr ) )){

            $log->update_time = TIME_NOW;
            $log->error = '支付回调签名错误.'.json_encode($xmlArray2);
            $log->save();
            AJAX::error('支付回调签名错误');
        }

                

        //支付单的状态不是代付款
        if($log->success_time != 0)AJAX::error('该订单已付款！');


        $type = substr($out_trade_no,0,1);
        $order_id = substr($out_trade_no,-6);
        // echo $type.$order_id;die("\n");
        if($type == 'A'){
            $order = $orderDrivingModel->find($order_id);
            $trip = $tripModel->where(['type'=>1,'id'=>$order_id])->find();
        }elseif($type == 'B'){
            $order = $orderTaxiModel->find($order_id);
            $trip = $tripModel->where(['type'=>2,'id'=>$order_id])->find();
        }elseif($type == 'C'){
            $order = $orderWayModel->find($order_id);
            $trip = $tripModel->where(['type'=>3,'id'=>$order_id])->find();
        }else{
            echo 'fail';die();
        }

        !$trip && AJAX::error('行程不存在');
        if($trip->statuss>45 && $trip->pay_type == 1){
            echo 'success';die();
        }
        $trip->statuss != 45 && AJAX::error('无法支付该订单');
        !$order && AJAX::error('订单不存在');

        $this->pay_finish($trip,$order);
        

        $log->update_time = TIME_NOW;
        $log->success_time = TIME_NOW;
        $log->open_id = $xmlArray['openid']?$xmlArray['openid']:'';
        $log->bank = $xmlArray['bank_type']?$xmlArray['bank_type']:'';
        $log->open_order_id = $xmlArray['transaction_id']?$xmlArray['transaction_id']:'';
        $log->name = $xmlArray['device_info']?$xmlArray['device_info']:'';
        $log->success_date = date('Y-m-d',TIME_NOW);
        $status = $log->save()->getStatus();

                
        echo 'success';

        
    }


    


    private function pay_finish($trip,$order){

        

    }

    
    

}