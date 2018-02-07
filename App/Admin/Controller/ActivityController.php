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
use App\Car\Model\ActivityModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

# 活动管理
class ActivityController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'activity';

    }


    # 活动管理
    function activity(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_activity(ActivityModel $model,$page = 1,$limit = 10,$search,$car_number){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(16);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        # 设置名字
        $m->setName('活动');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('优先级');
        $m->setHead('标题');
        $m->setHead('状态');
        $m->setHead('发布时间');
       
        # 设置表体
        $m->setBody('id');
        $m->setBody('level');
        $m->setBody('name');
        $m->setBody(['name'=>'status','type'=>'checkbox']);
        $m->setBody('create_date');

        
        # 筛选
        $m->where = [];
        # 获取列表
        $model->order('level desc','create_time desc');
        $m->getList(0);
        $m->each(function(&$v){
            $v->create_date = date('Y-m-d H:i:s',$v->create_time);
        });
        $m->output();

    }
    function admin_activity_get(ActivityModel $model,$id){

        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(16);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'   =>  'hidden','name'  =>  'id']);
        $m->setBody(['title'  =>  '标题','name'  =>  'name','size'  =>  '4']);
        $m->setBody(['title'  =>  '优先级','name'  =>  'level','size'  =>  '1','default'=>'0']);
        $m->setBody(['title'  =>  '图片','name'  =>  'thumb','type'  =>  'pic']);
        $m->setBody(['title'  =>  '类别','name'=>'type','type'=>'radio','option'=>['0'=>'图文活动','1'=>'外部链接'],'default'=>'0']);
        $m->setBody(['title'  =>  '状态','name'  =>  'status','type'=>'radio','option'=>['0'=>'关闭','1'=>'开启'],'default'=>'1']);
        $m->setBody(['title'  =>  '外部链接','name'  =>  'link','size'  =>  '6','placeholder'=>'请输入网址']);
        $m->setBody(['title'  =>  '详情','name'  =>  'detail','type'=>'h5']);
        
        # 设置名字
        $m->setName('活动管理');
        $m->getInfo();
        $m->output();
    }
    function admin_activity_upd(ActivityModel $model,$id,$pwd,$active,$birth){

        $this->L->adminPermissionCheck(16);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);
        if(!$id)$data['create_time'] = TIME_NOW;

        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_activity_del(ActivityModel $model,$id){
        $this->L->adminPermissionCheck(16);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

}