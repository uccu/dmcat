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
use fengqi\Hanzi\Hanzi;


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
        $list = $parkingLotModel->select(['*,groups.areaName AS group_name,district.areaName AS district_name,district.parent.areaName AS city_name,district.parent.parent2.areaName AS province_name,round(6378.138*2*asin(sqrt(pow(sin((%f*pi()/180-latitude*pi()/180)/2),2)+cos(%f*pi()/180)*cos(latitude*pi()/180)*pow(sin((%f*pi()/180-longitude*pi()/180)/2),2)))*1000) as distance',$latitude,$latitude,$longitude],'raw')->where('round(6378.138*2*asin(sqrt(pow(sin((%f*pi()/180-latitude*pi()/180)/2),2)+cos(%f*pi()/180)*cos(latitude*pi()/180)*pow(sin((%f*pi()/180-longitude*pi()/180)/2),2)))*1000) <= %d',$latitude,$latitude,$longitude,$distance*1000)->order('distance','raw')->get()->toArray();

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
        $info = $parkingLotModel->select(['*,groups.areaName AS group_name,district.areaName AS district_name,district.parent.areaName AS city_name,district.parent.parent2.areaName AS province_name,round(6378.138*2*asin(sqrt(pow(sin((%f*pi()/180-latitude*pi()/180)/2),2)+cos(%f*pi()/180)*cos(latitude*pi()/180)*pow(sin((%f*pi()/180-longitude*pi()/180)/2),2)))*1000) as distance',$latitude,$latitude,$longitude],'raw')->find($id);

        if(!$info)AJAX::error('停车场不存在');

        $out['info'] = $info;

        AJAX::success($out);



    }

    
    function auto(AreaModel $areaModel,ParkingLotModel $parkingLotModel,$page = 1){
        
        $groups = [];
        if(1){
            $data['key'] = $this->L->config->GAODE_KEY;
            $data['types'] = '150900';
            $data['city'] = '上海';
            $data['keywords'] = '长宁区';
            $data['extensions'] = 'all';
            $data['page'] = $page;
            $s = Func::curl('http://restapi.amap.com/v3/place/text',$data);

            $s = json_decode($s);


            $count = count($s->pois);

            $ff = 0;

            

            foreach($s->pois as $v){

                $location = explode(',',$v->location);
                $d = [];
                $d['gaode_id'] = $v->id;
                $d['name'] = $v->name;

                $d['district_id'] = $areaModel->where('parent_id=%d AND areaName=%n',4522848,$v->adname)->find()->id;
                if(!$d['district_id'])$d['district_id'] = 0;

                if(!$v->business_area)$v->business_area = '';
                if(is_array($v->business_area))$v->business_area[0];

                if($v->business_area){

                    if($groups[$v->business_area])$d['group_id'] = $groups[$v->business_area];
                    else $d['group_id'] = $areaModel->where('parent_id=%d AND areaName=%n',$d['district_id'],$v->business_area)->find()->id;

                    
                }
                if(!$d['group_id'] && $d['district_id'] && $v->business_area){

                    $data2['pinyin'] = Hanzi::pinyin($v->business_area)['pinyin'];
                    $data2['first'] = strtoupper($pinyin[0]);
                    $data2['level'] = 3;
                    $data2['addTime'] = date('Y-m-d H:i:s');
                    $data2['areaName'] = $v->business_area;
                    $data2['parent_id'] = $d['district_id'];


                    $d['group_id'] = $areaModel->set($data2)->add()->getStatus();
                    $groups[$v->business_area] = $d['group_id'];

                }
                if(!$d['group_id'])$d['group_id'] = 0;


                $d['address'] = $v->address;
                $d['create_time'] = TIME_NOW;
                $d['latitude'] = $location[1];
                $d['longitude'] = $location[0];
                $d['thumb'] = 'nopic.jpg';

                if(!$parkingLotModel->where('gaode_id=%n',$v->id)->find()){
                    $parkingLotModel->set($d)->add();
                    $ff++;
                }else{
                    $parkingLotModel->where('gaode_id=%n',$v->id)->set($d)->save();
                }
            }

            
        }

        $ss['count'] = $count;
        $ss['page'] = $page;
        $ss['ff'] = $ff;

        AJAX::success($ss);
    }
    
}