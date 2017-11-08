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
use App\Car\Model\H5Model;
use App\Car\Middleware\L;
use Model;

class HomeController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }


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

    function h5($id,H5Model $model){

        $m = $model->find($id);

        if($m)View::addData(['title'=>$m->name,'content'=>$m->content]);

        View::hamlReader('h5','App');
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
    function getLocationInfo(UserCouponModel $userCouponModel,$start_latitude,$start_longitude,$end_latitude,$end_longitude,AreaModel $areaModel,$num = 1,$type = 0,$time,$timeLine){

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
        if($type == 1){
            if($timeLine)$time = date('H:i',$timeLine);
            if(!$time)$time = date('H:i');
            $data = Func::getDrivingPrice($area->cityId,$time,$distance);
            $price = $data['total'];
            $out['start_price'] = $data['start'];
        }
        else{
            $price = Func::getEstimatedPrice($distance->distance);
            $out['start_price'] = '20.00';
        }
        if(!$price)AJAX::error('预估价获取失败');

        $out['area']        = $area;
        $out['distance']    = $distance;
        

        switch($num){

            case 2:
                $price = $price * 1.7;
                break;
            case 3:
                $price = $price * 2.8;
                break;
            default:
                break;
        }
            
        $out['price']       = $price;
        
        $out['coupon']      = '0.00';

        // $this->L->id = 43;
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


    function checkCity($start_latitude,$start_longitude,$end_latitude,$end_longitude){

        $area = Func::getArea($end_latitude,$start_longitude);
        $area2 = Func::getArea($end_latitude,$end_longitude);
        if(!$area || !$area2)AJAX:: error('地址坐标获取失败');
        $area->city == $area2->city && AJAX::error('起始地与目的地不能同市！');

        AJAX::success();
    }


    function brand(){

        $list = Model::copyMutiInstance('car_brand')->order('pinyin')->get()->toArray();

        $list2 = [];
        foreach($list as $v){
            $list2[$v->first][] = $v;
        }
        $list3 = [];
        foreach($list2 as $k=>$v){
            $list3[] = [
                'key'=>$k,
                'value'=>$v
            ];
        }

        $out['list'] = $list3;
        AJAX::success($out);
    }

    function model($brand_id){

        $list = Model::copyMutiInstance('car_model')->where(['brand_id'=>$brand_id])->order('pinyin')->get()->toArray();

        $out['list'] = $list;
        AJAX::success($out);
    }

    function color(){

        $list = Model::copyMutiInstance('color')->order('pinyin')->get()->toArray();

        $out['list'] = $list;
        AJAX::success($out);
    }

    function questionList(){

        $list = Model::copyMutiInstance('question')->selectExcept('content')->get()->toArray();

        $out['list'] = $list;
        AJAX::success($out);
    }

    function questionInfo($id){

        $info = Model::copyMutiInstance('question')->find($id);
        !$info && AJAX::error('错误！');
        $out['info'] = $info;
        AJAX::success($out);
    }

    function questionInfo_h5($id){

        $info = Model::copyMutiInstance('question')->find($id);
        !$info && AJAX::error('错误！');

        View::addData(['title'=>$info->name,'content'=>$info->content]);
        View::hamlReader('h5','App');
    }

    function getVersion(){

        $info['driver_version'] = $this->L->config->driver_version;
        $info['driver_version_file'] = $this->L->config->driver_version_file;
        
        if($info['driver_version_file']){

            $info['driver_version_file_path'] = Func::fullAddr('download/getVersionFile_driver');
        }

        $info['user_version'] = $this->L->config->user_version;
        $info['user_version_file'] = $this->L->config->user_version_file;

        if($info['user_version_file']){

            $info['user_version_file_path'] = Func::fullAddr('download/getVersionFile_user');
        }

        AJAX::success($info);
    }

    function te(){

        $a = '01:11'<'01:12';
        $b = '01:11'<'01:10';
        $c = '01:11'<'01:11';

        var_dump($a,$b,$c);
    }
    
}