<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;
use DB;

# 数据模型
use App\Car\Model\UserModel;
use App\Car\Model\UserIncomeModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;


class FansController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controllerName = 'fans';

    }

    function fans($parent_id = 0){

        View::addData(['getList'=>'admin_'.__FUNCTION__.'?a=1&parent_id='.$parent_id]);
        View::hamlReader('home/list','Admin');
    }

    function admin_fans(UserModel $model,$page=1,$limit=10,$parent_id = 0,$search){

        $m = Lists::getSingleInstance($model,$page,$limit);

        # 权限
        // $m->checkPermission(126);

        # 允许操作接口
        // $m->setOpt('get','../'.$this->controllerName.'/'.__FUNCTION__.'_get');
        // $m->setOpt('del','../'.$this->controllerName.'/'.__FUNCTION__.'_del');
        // $m->setOpt('upd','../'.$this->controllerName.'/'.__FUNCTION__.'_upd');
        // $m->setOpt('view','home/upd');
        // $m->setOpt('add','home/upd');
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);


        # 设置表头
        $m->setHead('');
        $m->setHead('ID');
        $m->setHead('名字');
        $m->setHead('手机号');
        $m->setHead('推荐人数');
        $m->setHead('本周收入');
        $m->setHead('本月收入');
        $m->setHead('总收入');

        # 设置表体
        $m->setBody(['type'=>'pic','name'=>'avatar','size'=> "30"]);
        $m->setBody('id');
        $m->setBody('name');
        $m->setBody('phone');
        $m->setBody(['name'=>'count','href'=>true]);
        $m->setBody('week_income');
        $m->setBody('month_income');
        $m->setBody('all_income');

        # 设置名字
        $m->setName();

        

        # 筛选
        $m->where = [];
        if($parent_id){
            $m->where['parent_id'] = $parent_id;
        }

        if($search){
            $m->where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        # 获取列表
        $model->select('id','avatar','name','phone');
        $m->getList(0);

        $m->fullPicAddr('avatar');

        $week = TIME_TODAY - (date('w') - 1) * 24 * 3600;
        $userIncomeModel = UserIncomeModel::copyMutiInstance();

        $m->each(function(&$v) use ($model,$week,$userIncomeModel){
            $v->count = $model->where(['parent_id'=>$v->id])->select('COUNT(*) AS c','RAW')->find()->c;
            if($v->count)$v->count_href = 'fans?parent_id='.$v->id;

            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['user_id'=>$v->id])->where('create_time>%n',$week)->find()->m;
            if(!$money)$money = '0.00';
            $v->week_income = $money;
            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['user_id'=>$v->id])->where(['month'=>date('Ym')])->find()->m;
            if(!$money)$money = '0.00';
            $v->month_income = $money;
            $money = $userIncomeModel->select('SUM(`money`) AS m','RAW')->where(['user_id'=>$v->id])->find()->m;
            if(!$money)$money = '0.00';
            $v->all_income = $money;
        });


        $m->output();

    }
    function admin_tag_get(TagModel $model,$id){
        
        $this->L->adminPermissionCheck(130);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_tag_get',
            'upd'   => '../home/admin_tag_upd',
            'back'  => 'home/tag',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '名字',
                'name'  =>  'name',

            ],
                
                
                
                
            ];
            
        !$model->field && AJAX::error('字段没有公有化！');
            
            
        $info = AdminFunc::get($model,$id);
        if($info->status != 0)$tbody[1]['disabled'] = true;
            
        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;
            
        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
        AJAX::success($out);
            
    }
    
    
    
}
    