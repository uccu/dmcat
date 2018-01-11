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
use App\Car\Model\UserModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

# 活动管理
class ActivityController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'user';

    }

    public function encrypt_password($password,$salt){
        return sha1($this->salt.md5($password).$salt);
    }

    # 活动管理
    function activity(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_activity(ActivityModel $model,$page = 1,$limit = 10,$search,$car_number){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(5);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        $m->setOptReq(['title'=>'用户搜索','name'=>'search','size'=>'3']);
        $m->setOptReq(['title'=>'车牌号搜索','name'=>'car_number','size'=>'3']);
        # 设置名字
        $m->setName('用户');
        # 设置表头
        $m->setHead('');
        $m->setHead('编号');
        $m->setHead('账号');
        $m->setHead('姓名');
        $m->setHead('出生年月');
        $m->setHead('车牌号1');
        $m->setHead('车牌号2');
        $m->setHead('车牌号3');
        $m->setHead('启用');
        # 设置表体
        $m->setBody(['name'=>'avatar','type'=>'pic','href'=>false,'size'=>'30']);
        $m->setBody('id');
        $m->setBody('phone');
        $m->setBody('name');
        $m->setBody('birth');
        $m->setBody('car_number_1');
        $m->setBody('car_number_2');
        $m->setBody('car_number_3');
        $m->setBody(['name'=>'active','type'=>'checkbox']);
        # 筛选
        $m->where = [];
        $search && $m->where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        $car_number && $m->where['car_number'] = ['car_number_1 = %n OR car_number_2 LIKE %n OR car_number_3 LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%'];
        # 获取列表
        $model->order('create_time desc');
        $m->getList(0);
        $m->fullPicAddr('avatar');
        $m->output();

    }
    function admin_activity_get(ActivityModel $model,$id){
        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(5);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'  =>  'hidden','name'  =>  'id']);
        $m->setBody(['title'  =>  '账号(手机号)','name'  =>  'phone','size'  =>  '2']);
        $m->setBody(['title'  =>  '名字','name'  =>  'name','size'  =>  '2']);
        $m->setBody(['title'  =>  '出生年月','name'  =>  'birth','size'  =>  '2','placeholder'=>'格式：YYYY-mm-dd']);
        $m->setBody(['title'  =>  '车牌号1','name'  =>  'car_number_1','size'  =>  '2']);
        $m->setBody(['title'  =>  '车牌号2','name'  =>  'car_number_2','size'  =>  '2']);
        $m->setBody(['title'  =>  '车牌号3','name'  =>  'car_number_3','size'  =>  '2']);
        $m->setBody(['title'  =>  '头像','name'  =>  'avatar','type'=>'avatar']);
        $m->setBody(['title'  =>  '修改密码','name'  =>  'pwd','size'  =>  '2']);
        
        # 设置名字
        $m->setName('用户管理');
        $m->getInfo();
        $m->output();
    }
    function admin_activity_upd(ActivityModel $model,$id,$pwd,$active,$birth){

        $this->L->adminPermissionCheck(5);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['salt']);
        unset($data['id']);

        // var_dump(strlen($birth));
        
        (is_null($active) && (strlen($birth) != 10 || !strtotime($birth))) && AJAX::error('生日的格式错误');


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
    function admin_activity_del(ActivityModel $model,$id){
        $this->L->adminPermissionCheck(5);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

}