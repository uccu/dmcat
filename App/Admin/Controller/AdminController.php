<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\Car\Model\AdminModel;
use App\Car\Model\AreaModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

# 管理员管理
class AdminController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'admin';

    }

    public function encrypt_password($password,$salt){
        return sha1($this->salt.md5($password).$salt);
    }

    
    # 平台管理员
    function admin(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_admin(AdminModel $model,$page = 1,$limit = 10,$search){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(2);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);
        # 设置名字
        $m->setName('管理员');
        # 设置表头
        $m->setHead('');
        $m->setHead('编号');
        $m->setHead('账号');
        $m->setHead('姓名');
        $m->setHead('电话');
        $m->setHead('启用');
        # 设置表体
        $m->setBody(['name'=>'avatar','type'=>'pic','href'=>false,'size'=>'30']);
        $m->setBody('id');
        $m->setBody('phone');
        $m->setBody('name');
        $m->setBody('mobile');
        $m->setBody(['name'=>'active','type'=>'checkbox',]);
        # 筛选
        $m->where = [];
        $m->where['type'] = 6;
        $search && $m->where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        # 获取列表
        $model->order('create_time desc');
        $m->getList(0);
        $m->fullPicAddr('avatar');
        $m->output();

    }
    function admin_admin_get(AdminModel $model,$id){
        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(2);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'  =>  'hidden','name'  =>  'id']);
        $m->setBody(['title'  =>  '账号','name'  =>  'phone','size'  =>  '4']);
        $m->setBody(['title'  =>  '手机号','name'  =>  'mobile','size'  =>  '4']);
        $m->setBody(['title'  =>  '名字','name'  =>  'name','size'  =>  '4']);
        $m->setBody(['title'  =>  '头像','name'  =>  'avatar','type'=>'avatar']);
        $m->setBody(['title'  =>  '修改密码','name'  =>  'pwd','size'  =>  '4']);
        # 设置名字
        $m->setName('用户管理');
        $m->getInfo();
        $m->output();
    }
    function admin_admin_upd(AdminModel $model,$id,$pwd,$active){

        $this->L->adminPermissionCheck(2);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['salt']);
        unset($data['id']);
        $data['type'] = 6;

        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
            $data['create_time'] = TIME_NOW;
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_admin_del(AdminModel $model,$id){
        $this->L->adminPermissionCheck(2);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

    # 停车场管理员
    function stop(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_stop(AdminModel $model,AreaModel $areaModel,$page = 1,$limit = 10,$search,$province_id = '0',$city_id = '0',$district_id = '0'){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(2);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);

        $city_id && $areaModel->find($city_id)->parent_id != $province_id && $city_id = '0';
        $district_id && $areaModel->find($district_id)->parent_id != $city_id && $district_id = '0';
        

        $provinces = $areaModel->where('level=0')->order('pinyin')->get_field('areaName','id');
        $citys = $province_id ? $areaModel->where('parent_id=%d',$province_id)->order('pinyin')->get_field('areaName','id') : [];
        $districts = $city_id ? $areaModel->where('parent_id=%d',$city_id)->order('pinyin')->get_field('areaName','id') : [];

        $z1 = $m->setOptReq(['title'=>'省','name'=>'province_id','type'=>'select','size'=>'2','default'=>$province_id]);
        $z2 = $m->setOptReq(['title'=>'市','name'=>'city_id','type'=>'select','size'=>'2','default'=>$city_id]);
        $z3 = $m->setOptReq(['title'=>'区','name'=>'district_id','type'=>'select','size'=>'2','default'=>$district_id]);

        

        $m->opt['req'][$z1]['option'] = $provinces;
        $m->opt['req'][$z2]['option'] = $citys;
        $m->opt['req'][$z3]['option'] = $districts;
        $m->opt['req'][$z1]['option']['0'] = '选择省';
        $m->opt['req'][$z2]['option']['0'] = '选择市';
        $m->opt['req'][$z3]['option']['0'] = '选择区';

        # 设置名字
        $m->setName('管理员');
        # 设置表头
        $m->setHead('');
        $m->setHead('序号');
        $m->setHead('账号');
        $m->setHead('姓名');
        $m->setHead('电话');
        $m->setHead('停车场名称');
        $m->setHead('省');
        $m->setHead('市');
        $m->setHead('区');
        $m->setHead('地址');
        $m->setHead('启用');
        # 设置表体
        $m->setBody(['name'=>'avatar','type'=>'pic','href'=>false,'size'=>'30']);
        $m->setBody('id');
        $m->setBody('phone');
        $m->setBody('name');
        $m->setBody('mobile');
        $m->setBody('parking_lot_name');
        $m->setBody('province_name');
        $m->setBody('city_name');
        $m->setBody('district_name');
        $m->setBody('address');
        $m->setBody(['name'=>'active','type'=>'checkbox',]);
        # 筛选
        $m->where = [];
        $m->where['type'] = 1;

        if($district_id)$m->where['district_id'] = $district_id;
        elseif($city_id)$m->where['city_id'] = $city_id;
        elseif($province_id)$m->where['province_id'] = $province_id;

        $search && $m->where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        # 获取列表
        $model->select('*','province.areaName>province_name','city.areaName>city_name','district.areaName>district_name')->order('create_time desc');
        $m->getList(0);
        $m->fullPicAddr('avatar');
        $m->output();

    }

    function admin_stop_get(AdminModel $model,$id){
        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(2);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'  =>  'hidden','name'  =>  'id']);
        $m->setBody(['title'  =>  '账号','name'  =>  'phone','size'  =>  '2']);
        $m->setBody(['title'  =>  '手机号','name'  =>  'mobile','size'  =>  '2']);
        $m->setBody(['title'  =>  '名字','name'  =>  'name','size'  =>  '2']);
        $m->setBody(['title'  =>  '头像','name'  =>  'avatar','type'=>'avatar']);
        $m->setBody(['title'  =>  '修改密码','name'  =>  'pwd','size'  =>  '2']);
        $m->setBody(['title'  =>  '停车场名称','name'  =>  'parking_lot_id','size'  =>  '2']);
        $m->setBody(['type'  =>  'selects','url'   =>  '/home/area',
            'detail'=>[
                ['name'=>'province_id' ,'title' =>  '省'],
                ['name'=>'city_id'     ,'title' =>  '市'],
                ['name'=>'district_id' ,'title' =>  '区']
            ]
        ]);
        $m->setBody(['title'  =>  '地址','name'  =>  'address','size'  =>  '4']);
        # 设置名字
        $m->setName('用户管理');
        $m->getInfo();
        $m->output();
    }
    function admin_stop_upd(AdminModel $model,$id,$pwd,$active){

        $this->L->adminPermissionCheck(2);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['salt']);
        unset($data['id']);
        $data['type'] = 1;

        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
            $data['create_time'] = TIME_NOW;
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_stop_del(AdminModel $model,$id){
        $this->L->adminPermissionCheck(2);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    # 设备管理员
    function equip(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_equip(AdminModel $model,$page = 1,$limit = 10,$search){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(2);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);
        # 设置名字
        $m->setName('管理员');
        # 设置表头
        $m->setHead('');
        $m->setHead('编号');
        $m->setHead('账号');
        $m->setHead('姓名');
        $m->setHead('电话');
        $m->setHead('启用');
        # 设置表体
        $m->setBody(['name'=>'avatar','type'=>'pic','href'=>false,'size'=>'30']);
        $m->setBody('id');
        $m->setBody('phone');
        $m->setBody('name');
        $m->setBody('mobile');
        $m->setBody(['name'=>'active','type'=>'checkbox',]);
        # 筛选
        $m->where = [];
        $m->where['type'] = 2;
        $search && $m->where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        # 获取列表
        $model->order('create_time desc');
        $m->getList(0);
        $m->fullPicAddr('avatar');
        $m->output();

    }
    function admin_equip_get(AdminModel $model,$id){
        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(2);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'  =>  'hidden','name'  =>  'id']);
        $m->setBody(['title'  =>  '账号','name'  =>  'phone','size'  =>  '4']);
        $m->setBody(['title'  =>  '手机号','name'  =>  'mobile','size'  =>  '4']);
        $m->setBody(['title'  =>  '名字','name'  =>  'name','size'  =>  '4']);
        $m->setBody(['title'  =>  '头像','name'  =>  'avatar','type'=>'avatar']);
        $m->setBody(['title'  =>  '修改密码','name'  =>  'pwd','size'  =>  '4']);
        # 设置名字
        $m->setName('用户管理');
        $m->getInfo();
        $m->output();
    }
    function admin_equip_upd(AdminModel $model,$id,$pwd,$active){

        $this->L->adminPermissionCheck(2);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['salt']);
        unset($data['id']);
        $data['type'] = 2;

        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
            $data['create_time'] = TIME_NOW;
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_equip_del(AdminModel $model,$id){
        $this->L->adminPermissionCheck(2);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }
    


}