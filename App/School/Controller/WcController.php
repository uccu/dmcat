<?php

namespace App\School\Controller;

use App\School\Model\PaymentModel;
use App\School\Model\RecruitStudentsModel;
use App\School\Tool\Func;
use App\School\Middleware\L;
use Controller;
use Response;
use Request;
use App\School\Tool\AJAX;

class WcController extends Controller{

    public $appid;
    public $app_secret;

    function __construct(){

        $this->L = L::getInstance();
        $this->appid = $this->L->config->wc_appid;
        $this->app_secret = $this->L->config->wc_app_secret;


    }


    
    function roll($state = 'test'){

        $redirect_uri = urlencode( 'http://weixin.ivy-china.com/wc/getCode' );

        header('Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid
        .'&redirect_uri='.$redirect_uri
        .'&response_type=code&scope=snsapi_base&state='.$state
        .'#wechat_redirect');

    }

    function getCode($code,$state){

        if(!$code)die('微信连接失败！');

        $data['appid'] = $this->appid;
        $data['secret'] = $this->app_secret;
        $data['code'] = $code;
        $data['grant_type'] = 'authorization_code';


        $json = Func::curl('https://api.weixin.qq.com/sns/oauth2/access_token',$data);

        if(!$json)die('微信解析失败！');

        if(!$json->openid){
            var_dump($json);die();
        }

        Response::getInstance()->cookie('wc_openid',$json->openid,0);

        if($state == 'recruit'){

            header('Location:/recruit/language');
        }elseif($state == 'test'){
            
            echo 'test';
        }
    }

    function wcpay_c(){

        $postStr = file_get_contents ( 'php://input' );
        $xmlObject =  simplexml_load_string ( $postStr );
        $xmlArray = [];
        $xmlArray2 = [];
        foreach($xmlObject as $k=>$v){
            $xmlArray[$k] = $xmlArray2[$k] = $v.'';
        }
        $sign = $xmlArray['sign'];
        unset($xmlArray['sign']);
        ksort($xmlArray,SORT_STRING);
        if(!$xmlArray || !$xmlArray['result_code'])AJAX::error('微信支付回调失败');
        if($xmlArray['result_code'] != 'SUCCESS')AJAX::error('微信支付回调.'.$xmlArray['return_msg']);

        $signStr = '';

        foreach($xmlArray as $k=>$v){
            $signStr .= $k.'='.$v.'&';
        }
        $signStr .= 'key='.$this->L->config->wc_api;

        $model = PaymentModel::getInstance();
        $where['out_trade_no'] = $xmlArray['out_trade_no'];
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
        

        $log->update_time = TIME_NOW;
        $log->success_time = TIME_NOW;
        $log->open_id = $xmlArray['openid']?$xmlArray['openid']:'';
        $log->bank = $xmlArray['bank_type']?$xmlArray['bank_type']:'';
        $log->open_order_id = $xmlArray['transaction_id']?$xmlArray['transaction_id']:'';
        $log->name = $xmlArray['device_info']?$xmlArray['device_info']:'';
        $status = $log->save()->getStatus();

        if($status){

            RecruitStudentsModel::getInstance()->set(['pay_time'=>TIME_NOW])->where(['out_trade_no'=>$xmlArray['out_trade_no']])->save();
        }      
        

        AJAX::success();


    }

    function prepay($out_trade_no){

        $wc_openid = Request::getInstance()->cookie('wc_openid','');
        // $wc_openid = 'ofKRkwE3Dtge7UXbdy7kvpOpSxMQ';
        !$wc_openid && AJAX::error('请在微信操作！');


        $total_fee = 500;
        $total_fee100 = $total_fee*100;
        $nonce_str = Func::randWord(32,2);
        $nonce_str2 = Func::randWord(32,2);
        
        $p['appid']             = $this->L->config->wc_appid;
        $p['body']              = '报名费用';
        $p['mch_id']            = $this->L->config->wc_mch_id;
        $p['nonce_str']         = $nonce_str;
        $p['notify_url']        = Func::fullAddr('wc/wcpay_c');
        $p['out_trade_no']      = $out_trade_no;
        $p['spbill_create_ip']  = $_SERVER ["REMOTE_ADDR"];
        $p['total_fee']         = $total_fee100;
        // $p['total_fee']         = '1';
        $p['trade_type']        = 'JSAPI';
        $p['openid']            = $wc_openid;
            
        $xml = '<xml>';
        $sign = '';
        ksort($p,SORT_STRING);
        foreach ( $p as $key => $val ) {
            $sign .= trim ( $key ) . "=" . trim ( $val ) . "&";
            $xml .= "<" . trim ( $key ) . ">" . trim ( $val ) . "</" . trim ( $key ) . ">";
        }
        $sign .= 'key='.$this->L->config->wc_api;

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


        $data['ctime'] = TIME_NOW;
        $data['nonce_str'] = $nonce_str;
        $data['pay_type'] = 'wcpay';
        $data['total_fee'] = $total_fee;
        $data['out_trade_no'] = $out_trade_no;


        if(!$da){

            $data['error'] = '微信服务器访问超时/无法访问';

            $id = PaymentModel::getInstance()->set($data)->add()->getStatus();
            if(!$id)AJAX::success('支付单生成失败');

            AJAX::error($data['error']);
        }

        $result = simplexml_load_string ( $da );
        if($result->return_code.'' == 'FAIL'){

            $data['error'] = '微信通信失败.'.$result->return_msg;
            $id = PaymentModel::getInstance()->set($data)->add()->getStatus();
            if(!$id)AJAX::success('支付单生成失败');
            AJAX::error($data['error']);
        }
        if($result->result_code.'' == 'FAIL'){

            $data['error'] = '微信预支付交易失败.'.$result->err_code.'.'.$result->err_code_des;
            $id = PaymentModel::getInstance()->set($data)->add()->getStatus();
            if(!$id)AJAX::success('支付单生成失败');
            AJAX::error($data['error']);
        }

        $prepay_id = $result->prepay_id.'';

        $data['prepay_id'] = $prepay_id;
        $data['prepay_time'] = TIME_NOW;
        $data['pay_nonce_str'] = $nonce_str2;

        $data2['appId'] = $this->L->config->wc_appid;
        $data2['nonceStr'] = $nonce_str2;
        $data2['package'] = 'prepay_id='.$prepay_id;
        $data2['signType'] = 'MD5';
        $data2['timeStamp'] = TIME_NOW.'';

        ksort($data2,SORT_STRING);
        foreach ( $data2 as $key => $val )$signStr .= trim ( $key ) . "=" . trim ( $val ) . "&";
        $signStr .= 'key='.$this->L->config->wc_api;
        $data2['paySign'] = strtoupper ( md5 ( $signStr ) );

        $id = PaymentModel::getInstance()->set($data)->add()->getStatus();
        AJAX::success($data2);
            
    }

    function pay($out_trade_no){

        $id = $out_trade_no;

        include VIEW_ROOT.'App/recruit/'.__FUNCTION__.'.php';
    }

    


    


}