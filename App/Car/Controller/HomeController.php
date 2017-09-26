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
    function getLocationInfo($start_latitude,$start_longitude,$end_latitude,$end_longitude,AreaModel $areaModel,$type = 0){

        $area = Func::getArea($start_latitude,$start_longitude);
        if(!$area)AJAX::error('位置获取失败');

        $area->cityId = $areaModel->where(['areaName'=>$area->city,'area_t.areaName'=>$area->province])->find()->id;
        !$area->cityId && AJAX::error('区域ID获取失败！');

        $distance = Func::getDistance($start_latitude,$start_longitude,$end_latitude,$end_longitude);
        if(!$distance)AJAX::error('距离获取失败');

        $price = Func::getEstimatedPrice($distance->distance);
        if(!$price)AJAX::error('预估价获取失败');

        $out['area'] = $area;
        $out['distance'] = $distance;
        $out['price'] = $price;
        $out['totalPrice'] = $price;
        $out['coupon'] = '0.00';

        AJAX::success($out);

    }


}