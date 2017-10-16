<?php

namespace App\Car\Controller;

use Controller;
use Uccu\DmcatTool\Tool\AJAX;
use Response;
use View;
use Request;
use stdClass;
use App\Car\Tool\Func;
use App\Car\Model\AreaModel;
use App\Car\Model\UserCouponModel;
use Model;

class HomeController extends Controller{

    function upAvatar(){

        $out['path'] = Func::uploadFiles('file',100,100);
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);
    }

    function uploadPic(){

        $out['path'] = Func::uploadFiles('file');
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);

    }
    function uploadFile(){
        
        $id = Func::upload('file');
        if(!$id)AJAX::error('no file');
        $out['path'] = $id;
        $out['fpath'] = '/pic/file.jpg';
        $out['apath'] = Func::fullPicAddr('file.jpg');
        AJAX::success($out);
    }

    function getArea($latitude,$longitude){
        $out = Func::getArea($latitude,$longitude);
        if(!$out)AJAX::error('获取失败');
        AJAX::success($out);

    }

    function getDistance($start_latitude,$start_longitude,$end_latitude,$end_longitude){

        $out = Func::getDistance($start_latitude,$start_longitude,$end_latitude,$end_longitude);
        if(!$out)AJAX::error('获取失败');
        AJAX::success($out);
    }

    function getEstimatedPrice($distance,$type = 1){
        
        $price = Func::getEstimatedPrice($distance,$type);
        if(!$price)AJAX::error('获取失败');
        $out['price'] = $price;
        AJAX::success($out);
    }


    /** 获取地理位置信息
     * getLocationInfo
     * @param mixed $start_latitude 
     * @param mixed $start_longitude 
     * @param mixed $end_latitude 
     * @param mixed $end_longitude 
     * @param mixed $areaModel 
     * @param mixed $type 
     * @return mixed 
     */
    function getLocationInfo(UserCouponModel $userCouponModel,$start_latitude,$start_longitude,$end_latitude,$end_longitude,AreaModel $areaModel,$type = 0){

        $area = Func::getArea($start_latitude,$start_longitude);
        if(!$area)AJAX::error('位置获取失败');

        // $areaModel = Model('area_t');

        $area->cityId = $areaModel->where(['areaName'=>$area->city,'area_t.areaName'=>$area->province])->find()->id;

        if(!$area->cityId){

            $area->cityId = $areaModel->where(['areaName'=>$area->district,'area_t.areaName'=>$area->city])->find()->id;

        }

        !$area->cityId && AJAX::error('区域ID获取失败！');

        $distance = Func::getDistance($start_latitude,$start_longitude,$end_latitude,$end_longitude);
        if(!$distance)AJAX::error('距离获取失败');

        $price = Func::getEstimatedPrice($distance->distance);
        if(!$price)AJAX::error('预估价获取失败');

        $out['area']        = $area;
        $out['distance']    = $distance;
        $out['price']       = $price;
        
        $out['coupon']      = '0.00';

        if($this->L->id){

            $coupon = $userCouponModel->where(['user_id'=>$this->L->id])->where('end_time>%n',TIME_NOW)->where(['type'=>$type])->order('money desc')->find();
            if($coupon) $out['coupon'] = $coupon->money;
            
        }

        $out['totalPrice']  = $price - $out['coupon'];
        if($out['totalPrice'] < 0)$out['totalPrice'] = '0.00';
        

        AJAX::success($out);

    }



    /** 获取省市
     * area
     * @param mixed $id 
     * @param mixed $areaModel 
     * @return mixed 
     */
    function area($id,AreaModel $areaModel){

        if(!$id){

            $list = $areaModel->where(['parent_id'=>0])->order('pinyin')->get_field('areaName','id')->toArray();
        }else{

            $list = $areaModel->where(['parent_id'=>$id])->order('pinyin')->get_field('areaName','id')->toArray();
        }
        $out['list'] = $list;
        AJAX::success($out);
    }


     # 获取预约时间
    function time(){

        $h = date('H',TIME_NOW);
        $i = date('i',TIME_NOW);

        $t = TIME_TODAY + 2*24*3600;
        $t2 = TIME_TODAY + 24*3600;

        for($e = TIME_YESTERDAY;$e < $t;$e += 600){

            if($e < TIME_NOW){

            }elseif($e < $t2){
                $data['t1'][date('H',$e)][] = [
                    'timestamp'=>$e,
                    'name'=>date('i',$e)
                ];
            }else{
                $data['t2'][date('H',$e)][] = [
                    'timestamp'=>$e,
                    'name'=>date('i',$e)
                ];
            }


        }

        foreach($data['t1'] as $k=>&$v){

            $v = [
                'name'=>$k,
                'list'=>$v
            ];
        }
        $data['t1'] = array_values($data['t1']);

        foreach($data['t2'] as $k=>&$v){

            $v = [
                'name'=>$k,
                'list'=>$v
            ];
        }
        $data['t2'] = array_values($data['t2']);

        $data = [
            [
                'name'=>'今天',
                'list'=>$data['t1']
            ],[
                'name'=>'明天',
                'list'=>$data['t2']
            ]
        ];

        AJAX::success(['data'=>$data]);


    }


    function bank(){

        $list = Model::copyMutiInstance('bank')->get()->toArray();
        $out['list'] = $list;
        AJAX::success($out);
    }

}