<?php

namespace App\Admin\Controller;

use Controller;
use View;
use Uccu\DmcatHttp\Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;
use fengqi\Hanzi\Hanzi;
use Model;


# 数据模型
use App\Car\Model\AreaModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

# 活动管理
class GroupController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'group';

    }


    # 城市管理
    function city(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_city(AreaModel $model,$page = 1,$limit = 50,$search,$province_id){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(7);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        $p = $m->setOptReq(['title'=>'省','name'=>'province_id','type'=>'select','default'=>'0']);
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);

        $m->opt['req'][$p]['option'] = $model->select('id','areaName>value')->where('level=0')->order('pinyin')->get()->toArray();
        $m->opt['req'][$p]['option'] = array_merge(['选择省'],$m->opt['req'][$p]['option']);

        # 设置名字
        $m->setName('城市');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('省');
        $m->setHead('市');

       
        # 设置表体
        $m->setBody('id');
        $m->setBody('province_name');
        $m->setBody('areaName');

        
        # 筛选
        $m->where = [];
        $province_id && $m->where['parent_id'] = $province_id;
        $m->where['level'] = 1;
        $search && $m->where['search'] = ['areaName LIKE %n','%'.$search.'%'];
        # 获取列表
        $model->select('*','parent.areaName>province_name');
        $model->order('pinyin');
        $m->getList(0);

        $m->output();

    }
    function admin_city_get(AreaModel $model,$id){

        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(7);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'   =>  'hidden','name'  =>  'id']);
        $p = $m->setBody(['title'  =>  '所在省','name'  =>  'parent_id','type'  =>  'select','default'=>'0']);
        $m->setBody(['title'  =>  '所在市','name'  =>  'areaName','size'  =>  '4']);

        $m->tbody[$p]['option'] = $model->select('id','areaName>value')->where('level=0')->order('pinyin')->get()->toArray();
        $m->tbody[$p]['option'] = array_merge(['请选择'],$m->tbody[$p]['option']);
        
        # 设置名字
        $m->setName('城市管理');
        $m->getInfo();
        $m->output();
    }
    function admin_city_upd(AreaModel $model,$id,$areaName,$parent_id){

        $this->L->adminPermissionCheck(7);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);
        if(!$parent_id)AJAX::error('请选择省');

        $data['pinyin'] = Hanzi::pinyin($areaName)['pinyin'];
        $data['first'] = strtoupper($pinyin[0]);
        $data['level'] = 1;
        $data['addTime'] = date('Y-m-d H:i:s');

        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_city_del(AreaModel $model,$id){
        $this->L->adminPermissionCheck(7);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

    # 区域管理
    function area(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_area(AreaModel $model,$page = 1,$limit = 50,$search,$province_id,$city_id){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(8);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        $p = $m->setOptReq(['title'=>'省','name'=>'province_id','type'=>'select','default'=>'0']);
        $p2 = $m->setOptReq(['title'=>'市','name'=>'city_id','type'=>'select','default'=>'0']);
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);

        
        $city_id && $model->find($city_id)->parent_id != $province_id && $city_id = '0';
        $provinces = $model->select('id','areaName>value')->where('level=0')->order('pinyin')->get()->toArray();
        $citys = $province_id ? $model->select('id','areaName>value')->where('parent_id=%d',$province_id)->order('pinyin')->get()->toArray() : [];

        $m->opt['req'][$p]['option'] = $provinces;
        $m->opt['req'][$p]['option'] = array_merge(['选择省'],$m->opt['req'][$p]['option']);

        $m->opt['req'][$p2]['option'] = $citys;
        $m->opt['req'][$p2]['option'] = array_merge(['选择市'],$m->opt['req'][$p2]['option']);


        # 设置名字
        $m->setName('地区');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('省');
        $m->setHead('市');
        $m->setHead('区');

       
        # 设置表体
        $m->setBody('id');
        $m->setBody('province_name');
        $m->setBody('city_name');
        $m->setBody('areaName');

        
        # 筛选
        $m->where = [];
        if($city_id)$m->where['parent_id'] = $city_id;
        elseif($province_id)$m->where['parent.parent_id'] = $province_id;
        $m->where['level'] = 2;
        $search && $m->where['search'] = ['areaName LIKE %n','%'.$search.'%'];
        # 获取列表
        $model->select('*','parent.areaName>city_name','parent.parent2.areaName>province_name');
        $model->order('pinyin');
        $m->getList(0);

        $m->output();

    }
    function admin_area_get(AreaModel $model,$id){

        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(8);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'   =>  'hidden','name'  =>  'id']);
        $p = $m->setBody([
                    
                'type'  =>  'selects',
                'url'   =>  '/home/area',
                'detail'=>[
                    ['name'=>'province_id' ,'title' =>  '所在省'],
                    ['name'=>'parent_id'   ,'title' =>  '所在市']
                ]
            ]);
        $m->setBody(['title'  =>  '所在区','name'  =>  'areaName','size'  =>  '4']);

        
        
        # 设置名字
        $m->setName('地区管理');

        $model->select('*','parent.parent_id>province_id');

        $m->getInfo();
        $m->output();
    }
    function admin_area_upd(AreaModel $model,$id,$areaName,$parent_id){

        $this->L->adminPermissionCheck(8);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);
        if(!$parent_id)AJAX::error('请选择市');

        $data['pinyin'] = Hanzi::pinyin($areaName)['pinyin'];
        $data['first'] = strtoupper($pinyin[0]);
        $data['level'] = 2;
        $data['addTime'] = date('Y-m-d H:i:s');

        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_area_del(AreaModel $model,$id){
        $this->L->adminPermissionCheck(8);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    # 商圈管理
    function group(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_group(AreaModel $model,$page = 1,$limit = 50,$search,$province_id,$city_id,$district_id){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(9);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        $p = $m->setOptReq(['title'=>'省','name'=>'province_id','type'=>'select','default'=>'0']);
        $p2 = $m->setOptReq(['title'=>'市','name'=>'city_id','type'=>'select','default'=>'0']);
        $p3 = $m->setOptReq(['title'=>'区','name'=>'district_id','type'=>'select','default'=>'0']);
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);

        
        $city_id && $model->find($city_id)->parent_id != $province_id && $city_id = '0';
        $district_id && $model->find($district_id)->parent_id != $city_id && $district_id = '0';

        $provinces = $model->select('id','areaName>value')->where('level=0')->order('pinyin')->get()->toArray();
        $citys = $province_id ? $model->select('id','areaName>value')->where('parent_id=%d',$province_id)->order('pinyin')->get()->toArray() : [];
        $districts = $city_id ? $model->select('id','areaName>value')->where('parent_id=%d',$city_id)->order('pinyin')->get()->toArray() : [];

        $m->opt['req'][$p]['option'] = $provinces;
        $m->opt['req'][$p]['option'] = array_merge(['选择省'],$m->opt['req'][$p]['option']);

        $m->opt['req'][$p2]['option'] = $citys;
        $m->opt['req'][$p2]['option'] = array_merge(['选择市'],$m->opt['req'][$p2]['option']);

        $m->opt['req'][$p3]['option'] = $districts;
        $m->opt['req'][$p3]['option'] = array_merge(['选择区'],$m->opt['req'][$p3]['option']);


        # 设置名字
        $m->setName('商圈');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('省');
        $m->setHead('市');
        $m->setHead('区');
        $m->setHead('商圈');

       
        # 设置表体
        $m->setBody('id');
        $m->setBody('province_name');
        $m->setBody('city_name');
        $m->setBody('district_name');
        $m->setBody('areaName');

        
        # 筛选
        $m->where = [];
        if($district_id)$m->where['parent_id'] = $district_id;
        elseif($city_id)$m->where['parent.parent_id'] = $city_id;
        elseif($province_id)$m->where['parent.parent2.parent_id'] = $province_id;
        $m->where['level'] = 3;
        $search && $m->where['search'] = ['areaName LIKE %n','%'.$search.'%'];
        # 获取列表
        $model->select('*','parent.areaName>district_name','parent.parent2.areaName>city_name','parent.parent2.parent3.areaName>province_name');
        $model->order('pinyin');
        $m->getList(0);

        $m->output();

    }
    function admin_group_get(AreaModel $model,$id){

        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(9);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'   =>  'hidden','name'  =>  'id']);
        $p = $m->setBody([
                    
                'type'  =>  'selects',
                'url'   =>  '/home/area',
                'detail'=>[
                    ['name'=>'province_id' ,'title' =>  '所在省'],
                    ['name'=>'city_id'   ,'title' =>  '所在市'],
                    ['name'=>'parent_id'   ,'title' =>  '所在区'],
                ]
            ]);
        $m->setBody(['title'  =>  '所在商圈','name'  =>  'areaName','size'  =>  '4']);

        
        
        # 设置名字
        $m->setName('商圈管理');

        $model->select('*','parent.parent_id>city_id','parent.parent2.parent_id>province_id');

        $m->getInfo();
        $m->output();
    }
    function admin_group_upd(AreaModel $model,$id,$areaName,$parent_id){

        $this->L->adminPermissionCheck(9);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);
        if(!$parent_id)AJAX::error('请选择区');

        $data['pinyin'] = Hanzi::pinyin($areaName)['pinyin'];
        $data['first'] = strtoupper($pinyin[0]);
        $data['level'] = 3;
        $data['addTime'] = date('Y-m-d H:i:s');

        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_group_del(AreaModel $model,$id){
        $this->L->adminPermissionCheck(9);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


}