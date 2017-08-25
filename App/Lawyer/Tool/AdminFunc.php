<?php
namespace App\Lawyer\Tool;

use Model;
use stdClass;
use AJAX;
use App\Lawyer\Middleware\L2;
use App\Lawyer\Model\PaymentModel;


class AdminFunc{
    
    static function get(Model $model,$id,$ajax_error = false){

        $info = $model->find($id);

        $ajax_error && !$info && AJAX::error('查询失败！');
        
        if(!$info){

            $info = new stdClass;
            foreach($model->field as $field)$info->$field = '';
            
        }

        return $info;

    }


    static function upd(Model $model,$id,$data,$add = true){

        if(!$data)return 0;

        if($add && !$id){

            return $model->set($data)->add()->getStatus();

        }

        
        return $model->set($data)->save($id)->getStatus();

    }

    static function del(Model $model,$id){

        return $model->remove($id)->getStatus();

    }



    static function alipay_refund($payment_id,$money = 0){

        

        /*退款批次号*/
        $out_trade_no = TIME_NOW.Func::randWord(10,3);
        $paymentModel = PaymentModel::copyMutiInstance();

        $payLog = $paymentModel->find($payment_id);
        
        !$payLog && AJAX::error('没有找到支付记录!');
        !$payLog->success_time && AJAX::error('没有付款成功不能退款!');
        !$payLog->open_order_id && AJAX::error('未找到原付款支付宝交易号!');
        !$payLog->total_fee && AJAX::error('交易价格无效，不能退款!');

        if(!$money)$money = $payLog->total_fee;
        $money > $payLog->total_fee && AJAX::error('退款金额不能大于交易金额!');

        $open_order_id = $payLog->open_order_id;

        $L = L2::getSingleInstance();


        $p['service']           = 'refund_fastpay_by_platform_pwd';         // 服务接口名称， 固定值
        $p['partner']           = $L->config->aliay_partner;                // 签约的支付宝账号对应的支付宝唯一用户号
        $p['_input_charset']    = 'UTF-8';                                  // 参数编码， 固定值
        // $p['notify_url']        = Func::fullAddr('pay/alipay_refund_c');    // 服务器异步通知页面路径
        $p['seller_email']      = $L->config->aliay_seller_id;              // 卖家支付宝账号
        $p['seller_user_id']    = $L->config->aliay_partner;                // 卖家用户ID
        $p['refund_date']       = date('Y-m-d H:i:s',TIME_NOW);             // 退款请求的当前时间
        $p['batch_no']          = $out_trade_no;                            // 退款批次号
        $p['batch_num']         = '1';                                      // 总笔数
        $p['detail_data']       = $open_order_id.'^'.$money.'^退款';// 单笔数据集
        
        
        ksort($p);

        foreach($p as $k=>$v){
            $info2[] = $k.'='.$v ;
            $info[] = $k.'='.urlencode( $v );
        }
        $info2 = implode('&',$info2);
        $info = implode('&',$info);

        $md5 = 'm838by7lgm8lmfne8jbeibxmbsinin9c';
        
        
        $sign = md5($info2.$md5);
        // $sign = urlencode ( $sign );
        // 执行签名函数
        $info .= "&sign=" . $sign . "&sign_type=MD5";
        $p['sign'] = $sign;
        $p['sign_type'] = 'MD5';
        
        echo 'https://mapi.alipay.com/gateway.do?'.$info;die();

        return  Func::curl('https://mapi.alipay.com/gateway.do?'.$info);



    }
}