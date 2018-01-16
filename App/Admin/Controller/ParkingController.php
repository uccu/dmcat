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
use App\Car\Model\ParkingLotModel;
use App\Car\Model\AreaModel;
use App\Car\Model\AdminModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

# 活动管理
class ParkingController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'parking';

    }


    # 活动管理
    function lot(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_lot(ParkingLotModel $model,AreaModel $areaModel,AdminModel $adminModel,$page = 1,$limit = 10,$search,$province_id,$city_id,$district_id,$group_id,$com = -1){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(12);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');

        $p = $m->setOptReq(['title'=>'省','name'=>'province_id','type'=>'select','default'=>'0','size'=>'2']);
        $p2 = $m->setOptReq(['title'=>'市','name'=>'city_id','type'=>'select','default'=>'0','size'=>'2']);
        $p3 = $m->setOptReq(['title'=>'区','name'=>'district_id','type'=>'select','default'=>'0','size'=>'2']);
        $p4 = $m->setOptReq(['title'=>'商圈','name'=>'group_id','type'=>'select','default'=>'0','size'=>'2']);
        $m->setOptReq(['title'=>'是否合作','name'=>'com','type'=>'select','default'=>'-1','size'=>'2','option'=>[
            ['id'=>'-1','value'=>'全部'],
            ['id'=>'1','value'=>'合作'],
            ['id'=>'0','value'=>'非合作'],
        ]]);
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);

        $city_id && $areaModel->find($city_id)->parent_id != $province_id && $city_id = '0';
        $district_id && $areaModel->find($district_id)->parent_id != $city_id && $district_id = '0';
        $group_id && $areaModel->find($group_id)->parent_id != $district_id && $group_id = '0';

        $provinces = $areaModel->select('id','areaName>value')->where('level=0')->order('pinyin')->get()->toArray();
        $citys = $province_id ? $areaModel->select('id','areaName>value')->where('parent_id=%d',$province_id)->order('pinyin')->get()->toArray() : [];
        $districts = $city_id ? $areaModel->select('id','areaName>value')->where('parent_id=%d',$city_id)->order('pinyin')->get()->toArray() : [];
        $groups = $district_id ? $areaModel->select('id','areaName>value')->where('parent_id=%d',$district_id)->order('pinyin')->get()->toArray() : [];

        $m->opt['req'][$p]['option'] = array_merge(['选择省'],$provinces);
        $m->opt['req'][$p2]['option'] = array_merge(['选择市'],$citys);
        $m->opt['req'][$p3]['option'] = array_merge(['选择区'],$districts);
        $m->opt['req'][$p4]['option'] = array_merge(['选择商圈'],$groups);


        # 设置名字
        $m->setName('停车场');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('停车场名称');
        $m->setHead('停车场管理员');
        $m->setHead('省');
        $m->setHead('市');
        $m->setHead('区');
        $m->setHead('商圈');
        $m->setHead('地址');
        $m->setHead('车位');
        $m->setHead('收费/小时');
        $m->setHead('开启');
       
        # 设置表体
        $m->setBody('id');
        $m->setBody('name');
        $m->setBody('admin_name');
        $m->setBody('province_name');
        $m->setBody('city_name');
        $m->setBody('district_name');
        $m->setBody('group_name');
        $m->setBody('address');
        $m->setBody('parking_space');
        $m->setBody('fee');
        $m->setBody(['name'=>'active','type'=>'checkbox']);

        
        # 筛选
        $m->where = [];
        if($group_id)$m->where['group_id'] = $group_id;
        elseif($district_id)$m->where['district_id'] = $district_id;
        elseif($city_id)$m->where['district.parent_id'] = $city_id;
        elseif($province_id)$m->where['district.parent.parent_id'] = $province_id;

        if($com != '-1'){
            $m->where['com'] = $com;
        }

        $search && $m->where['search'] = ['name LIKE %n OR admin.name LIKE %n OR admin.mobile LIKE %n OR address LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%'];

        # 获取列表
        $model->order('create_time desc');
        $model->select('*','groups.areaName>group_name','district.areaName>district_name','district.parent.areaName>city_name','district.parent.parent2.areaName>province_name');
        $m->getList(0); 
        $m->each(function(&$v) USE ($adminModel){

            $v->parking_space = $v->empty . '/' . $v->count;

            $admin = $adminModel->where('parking_lot_id=%d',$v->id)->find();
            if($admin){
                $v->admin_name = $admin->mobile .'/'. $admin->name;
            }

            
        });
        $m->output();

    }
    function admin_lot_get(ParkingLotModel $model,$id){

        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(12);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'   =>  'hidden','name'  =>  'id']);
        $m->setBody(['title'  =>  '停车场名称','name'  =>  'name','size'  =>  '2']);
        $m->setBody(['title'  =>  '选择物业账号','name'  =>'admin','size'  =>'4','suggest'=>'/admin/admin/admin_stop?search=','fields'=>['phone'=>'账号','name'=>'名字','mobile'=>'手机号']]);
        $m->setBody([
                    
                'type'  =>  'selects',
                'url'   =>  '/home/area',
                'detail'=>[
                    ['name'=>'province_id' ,'title' =>  '所在省'],
                    ['name'=>'city_id'   ,'title' =>  '所在市'],
                    ['name'=>'district_id'   ,'title' =>  '所在区'],
                    ['name'=>'group_id'   ,'title' =>  '所在商圈'],
                ]
            ]);
        $m->setBody(['title'  =>  '详细地址','name'=>'address']);
        $m->setBody(['title'  =>  '图片','name'=>'thumb','type'=>'pic']);
        $m->setBody(['title'  =>  '车位数量','name'  =>  'count','default'=>'0','size'  =>  '1']);
        $m->setBody(['title'  =>  '空余车位','name'  =>  'empty','disabled'  =>  true,'default'=>'0','size'  =>  '1']);
        $m->setBody(['title'  =>  '收费标准/小时','name'  =>  'fee','size'  =>  '2','default'=>'0']);
        $m->setBody(['title'  =>  '营业时长','name'  =>  'open_time','size'  =>  '2','default'=>'全天']);
        $m->setBody(['title'  =>  '免费时长/小时','name'  =>  'free','size'  =>  '2','default'=>'0']);
        $m->setBody(['title'  =>  '封顶费用','name'  =>  'top_fee','size'  =>  '2']);
        $m->setBody(['title'  =>  '是否为合作关系','name'  =>  'com','type'=>'radio','option'=>['0'=>'否','1'=>'是'],'default'=>'0']);
        
        # 设置名字
        $m->setName('活动管理');
        $model->select('*','admin.name>admin','district.parent_id>city_id','district.parent.parent_id>province_id');
        $m->getInfo();
        $m->output();
    }
    function admin_lot_upd(ParkingLotModel $model,AdminModel $adminModel,$id,$pwd,$active,$admin){

        $this->L->adminPermissionCheck(12);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);
        if(!$id)$data['create_time'] = TIME_NOW;

        $upd = AdminFunc::upd($model,$id,$data);

        if(!$id)$id = $upd;

        if($admin && $id){
            $admin = $adminModel->where('phone=%n',$admin)->find();
            $admin2 = $adminModel->where('parking_lot_id=%d',$id)->find();

            if($admin2 && $admin2 != $admin){
                $admin2->parking_lot_id = 0;
                $admin2->save();
                $admin->parking_lot_id = $id;
                $admin->save();
            }elseif(!$admin2){
                $admin->parking_lot_id = $id;
                $admin->save();
            }
        }

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_lot_del(ParkingLotModel $model,$id){
        $this->L->adminPermissionCheck(12);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

}