<?php

namespace App\Admin\Controller;

use Controller;
use View;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;
use fengqi\Hanzi\Hanzi;
use Model;

# 数据模型
use App\Car\Model\DriverModel;
use App\Car\Model\AreaModel;
use App\Car\Model\TripModel;
use App\Car\Model\UserModel;



use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

class MapController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    function area(){
        $data['key'] = $this->L->config->GAODE_KEY;
        View::addData($data);
        View::addData(['getList'=>'admin_area']);
        View::hamlReader('map/drivers','Admin');
    }

    function admin_area(DriverModel $model,$type = 1,$province = '0',$city = '0',AreaModel $areaModel){
        
        if($type == 3){
            $model  = UserModel::copyMutiInstance();
        }

        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        $m->checkPermission(164);

        if($this->L->userInfo->type == 2 || $this->L->userInfo->type == 1){
            $provinceObj = $areaModel->select('areaName>value','id','areaName')->find($this->L->userInfo->province_id);
            $province = $provinceObj->id;
            $provinceName = $provinceObj->areaName;
            $areaName = $provinceName;
        }elseif($province){
            $provinceName = $areaModel->find($province)->areaName;
            $areaName = $provinceName;
        }
        if($province && $city){
            $cityObj = $areaModel->find($city);

            if($cityObj->parent_id != $province){
                $city = '0';$cityName = '';
            }else{
                $cityName = $cityObj->areaName;
            }
            $areaName = $provinceName.$cityName;
        }
        if($areaName){
            $location = Func::searchGeo($areaName);
            if($location){
                $m->other['location'] = explode(',',$location->location);
            }
        }


        # 允许操作接口

        $m->setOptReq(['title'=>'类型','name'=>'type','type'=>'select','default'=>'1','option'=>['1'=>'代驾','2'=>'出租车','3'=>'顺风车']]);
        
        if($this->L->userInfo->type == 2 || $this->L->userInfo->type == 1){

            $reqC1 = $m->setOptReq(['title'=>'省','name'=>'province','type'=>'select','default'=>'0']);
            $reqC2 = $m->setOptReq(['title'=>'市','name'=>'city','type'=>'select','default'=>'0']);
            
            $m->opt['req'][$reqC1]['option'] = [$provinceObj];
            $m->opt['req'][$reqC1]['default'] = $provinceObj->id;

        }else{
        
            $reqC1 = $m->setOptReq(['title'=>'省','name'=>'province','type'=>'select','default'=>$province]);
            $reqC2 = $m->setOptReq(['title'=>'市','name'=>'city','type'=>'select','default'=>$city]);
            $provinces = $areaModel->where(['level'=>0])->order('pinyin')->get_field('areaName','id')->toArray();
            $m->opt['req'][$reqC1]['option'] = $provinces;
            $m->opt['req'][$reqC1]['option']['0'] = '请选择';

        }

        if($this->L->userInfo->type == 1){

            if($province){
                $m->opt['req'][$reqC2]['option'] = $areaModel->where(['parent_id'=>$province])->where('id IN (%c)',explode(',',$this->L->userInfo->city_id))->order('pinyin')->get_field('areaName','id')->toArray();
            }
            $m->opt['req'][$reqC2]['option']['0'] = '请选择';

        }else{
            if($province){
                $m->opt['req'][$reqC2]['option'] = $areaModel->where(['parent_id'=>$province])->order('pinyin')->get_field('areaName','id')->toArray();
            }
            $m->opt['req'][$reqC2]['option']['0'] = '请选择';
        }

        # 设置名字
        $m->setName('代驾订单');
        
        

        # 筛选
        $m->where = [];
        $m->where['online'] = ['online.latitude>0'];

        if($this->L->userInfo->type == 2){
            $m->where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $m->where['city_id'] = ['%F IN (%c)','city_id', explode(',', $this->L->userInfo->city_id)];
        }

        
        if($type == 1)$m->where['type_driving'] = 1;
        elseif($type == 2)$m->where['type_taxi'] = 1;
        elseif($type == 3)$m->where['type'] = 1;
        if($city){
            $m->where['city_id'] = $city;
        }else{
            $m->where['id'] = 0;
        }
            
        
        # 获取列表
        if(in_array($type,[1,2]))$model->select('id','online.latitude','online.longitude','type_driving','type_taxi');
        elseif(in_array($type,[3]))$model->select('id','online.latitude','online.longitude');
        $m->getList(1);

        // $m->other['sql'] = $model->sql;

        $allCount = $nCount = $bCount = 0;

        $m->each(function(&$v) USE (&$allCount,&$nCount,&$bCount,$type){

            $v->type = $type;
            if(in_array($v->type,[1,2]))$v->busy =  TripModel::copyMutiInstance()->where(['driver_id'=>$v->id])->where('type IN (1,2) AND status IN (%c)',[20,25,30,35])->find() ? '1' : '0';
            else{
                $v->busy =  TripModel::copyMutiInstance()->where(['driver_id'=>$v->id])->where('type = 3 AND status IN (%c)',[20,25,30,35])->find() ? '1' : '0';
            }

            if($v->busy){
                $bCount++;
            }else{
                $nCount++;
            }
            $allCount++;

        });

        $m->other['allCount'] = $allCount;
        $m->other['nCount']   = $nCount;
        $m->other['bCount']   = $bCount;

        $m->output();

    }

}