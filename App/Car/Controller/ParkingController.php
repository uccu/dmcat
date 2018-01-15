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
use Model;

use App\Car\Model\ParkingLotModel;
use App\Car\Model\AreaModel;



class ParkingController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }


    /** 查找附近的停车场
     * lotNearby
     * @param mixed $distance 
     * @param mixed $parkingLotModel 
     * @param mixed $longitude 
     * @param mixed $latitude 
     * @return mixed 
     */
    function lotNearby($distance = 3,ParkingLotModel $parkingLotModel,$longitude = 0,$latitude = 0){

        // $latitude = 31.30027816;
        // $longitude = 121.41354076;
        $list = $parkingLotModel->select(['*,groups.areaName AS group_name,groups.parent.areaName AS district_name,groups.parent.parent2.areaName AS city_name,groups.parent.parent2.areaName AS province_name,round(6378.138*2*asin(sqrt(pow(sin((%f*pi()/180-latitude*pi()/180)/2),2)+cos(%f*pi()/180)*cos(latitude*pi()/180)*pow(sin((%f*pi()/180-longitude*pi()/180)/2),2)))*1000)/1000 as distance',$latitude,$latitude,$longitude],'raw')->where('round(6378.138*2*asin(sqrt(pow(sin((%f*pi()/180-latitude*pi()/180)/2),2)+cos(%f*pi()/180)*cos(latitude*pi()/180)*pow(sin((%f*pi()/180-longitude*pi()/180)/2),2)))*1000)/1000 <= %d',$latitude,$latitude,$longitude,$distance)->order('distance','raw')->get()->toArray();

        $out['list'] = $list;

        AJAX::success($out);
    }


    /** 获取单个停车场信息
     * lotInfo
     * @param mixed $parkingLotModel 
     * @param mixed $id 
     * @param mixed $longitude 
     * @param mixed $latitude 
     * @return mixed 
     */
    function lotInfo(ParkingLotModel $parkingLotModel,$id = 0,$longitude = 0,$latitude = 0){

        // $latitude = 31.30027816;
        // $longitude = 121.41354076;
        $info = $parkingLotModel->select(['*,groups.areaName AS group_name,groups.parent.areaName AS district_name,groups.parent.parent2.areaName AS city_name,groups.parent.parent2.areaName AS province_name,round(6378.138*2*asin(sqrt(pow(sin((%f*pi()/180-latitude*pi()/180)/2),2)+cos(%f*pi()/180)*cos(latitude*pi()/180)*pow(sin((%f*pi()/180-longitude*pi()/180)/2),2)))*1000)/1000 as distance',$latitude,$latitude,$longitude],'raw')->find($id);

        if(!$info)AJAX::error('停车场不存在');

        $out['info'] = $info;

        AJAX::success($out);



    }

    
    
}