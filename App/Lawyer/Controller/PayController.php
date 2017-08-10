<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;
use DB;
# Model

use App\Lawyer\Model\PaymentModel;
use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\ConsultPayRuleModel;

class PayController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }


    /** 获取价格
     * getPrice
     * @param mixed $type 
     * @param mixed $model 
     * @return mixed 
     */
    function getPrice($type,ConsultPayRuleModel $model){

        $price = $model->where(['type'=>$type])->find()->price;
        if(!$price)AJAX::error('未知的会员类型!');
        $out['price'] = $price;
        $out['type'] = $type;
        AJAX::success($out);
        
    }


    /** 模拟支付
     * fake_play
     * @param mixed $type 
     * @param mixed $model 
     * @param mixed $lmodel 
     * @return mixed 
     */
    function fake_play($type){

        $model  = ConsultPayRuleModel::copyMutiInstance();
        $lmodel = UserConsultLimitModel::copyMutiInstance();

        !$this->L->id && AJAX::error('未登录');
        $rule = $model->where(['type'=>$type])->find();
        if(!$rule)AJAX::error('未知的会员类型!');

        $this->pay_finish($rule->id,$this->L->id);
        AJAX::success();
    }


    private function pay_finish($rule_id,$user_id){

        $model  = ConsultPayRuleModel::copyMutiInstance();
        $lmodel = UserConsultLimitModel::copyMutiInstance();

        $rule = $model->find($rule_id);

        $limit = $lmodel->where(['user_id'=>$user_id,'rule_id'=>$rule->id])->find();
        if($limit){
            $limit->death_time < TIME_NOW && $limit->death_time = TIME_NOW;
            $limit->word_count == -1 && $limit->word_count = 0;
            $limit->question_count == -1 && $limit->question_count = 0;

            $limit->death_time += $rule->expiry * 24 * 3600;
            $limit->word_count += $rule->word_count;
            $limit->question_count += $rule->question_count;
            $limit->save();
        }else{

            $data['user_id'] = $user_id;
            $data['rule_id'] = $rule->id;
            $data['death_time'] = TIME_NOW + $rule->expiry * 24 * 3600;
            $data['word_count'] = $rule->word_count;
            $data['question_count'] = $rule->question_count;
            $data['pay_time'] = TIME_NOW;

            $lmodel->set($data)->add();
        }


        return true;
    }



    function alipay($type){

        $model  = ConsultPayRuleModel::copyMutiInstance();
        $lmodel = UserConsultLimitModel::copyMutiInstance();

        //登陆验证
        !$this->L->id && AJAX::error('请登录');

        /*验证会员类型是否存在*/
        $rule = $model->where(['type'=>$type])->find();
        if(!$rule)AJAX::error('未知的会员类型!');

        /*总价格&订单号*/
        $total_fee = $rule->price;
        $out_trade_no = TIME_NOW.Func::randWord(10,3);

        /*生成随机码*/
        $nonce_str = Func::randWord(32,2);



        $p['partner']           = $this->L->config->aliay_partner;          // 签约的支付宝账号对应的支付宝唯一用户号
        $p['seller_id']         = $this->L->config->aliay_seller_id;        // 签约卖家支付宝账号
        $p['out_trade_no']      = $out_trade_no;                            // 商户网站唯一订单号
        $p['subject']           = '续费会员';                                // 商品名称
        $p['body']              = '续费会员';                                // 商品详情
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
        $data['rule_id'] = $rule->id;

        
        $id = PaymentModel::getSingleInstance()->set($data)->add()->getStatus();
        if(!$id)AJAX::success('支付单生成失败');

        $data['param'] = $info;

        AJAX::success($data);

    }

    function wcpay($type){

        $model  = ConsultPayRuleModel::copyMutiInstance();
        $lmodel = UserConsultLimitModel::copyMutiInstance();

        //登陆验证
        !$this->L->id && AJAX::error('请登录');

        /*验证会员类型是否存在*/
        $rule = $model->where(['type'=>$type])->find();
        if(!$rule)AJAX::error('未知的会员类型!');

        /*总价格&订单号*/
        $total_fee = $rule->price;
        $total_fee100 = floor($total_fee*100);
        $out_trade_no = TIME_NOW.Func::randWord(10,3);

        /*生成随机码*/
        $nonce_str = Func::randWord(32,2);
        $nonce_str2 = Func::randWord(32,2);
        
        $p['appid']             = $this->L->config->wcpay_appid;
        $p['body']              = 'INAX 商品订单';
        $p['mch_id']            = $this->L->config->wcpay_mch_id;
        $p['nonce_str']         = $nonce_str;
        $p['notify_url']        = Func::fullAddr('goods/wcpay_c');
        $p['out_trade_no']      = $out_trade_no;
        $p['spbill_create_ip']  = $_SERVER ["REMOTE_ADDR"];
        $p['total_fee']         = $total_fee100;
        // $p['total_fee']         = '1';
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


    function alipay_c(){



    }

    function wcpay_c(){


        
    }

}