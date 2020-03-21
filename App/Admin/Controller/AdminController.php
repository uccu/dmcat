<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Uccu\DmcatHttp\Request;
use Uccu\DmcatTool\Tool\AJAX;

use App\Blog\Middleware\L;
use App\Resource\Tool\Func;
use App\Admin\Tool\AdminFunc;


# 数据模型
use App\Blog\Model\AdminModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

# 管理员管理
class AdminController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();
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
        $search && $m->where['search'] = ['name LIKE %n OR phone LIKE %n OR mobile LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%'];
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
    function admin_admin_upd(AdminModel $model,$id,$pwd){

        $this->L->adminPermissionCheck(2);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['salt']);
        unset($data['id']);
        $data['type'] = 6;

        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
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

}