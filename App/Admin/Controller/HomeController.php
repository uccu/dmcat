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
use App\Car\Model\H5Model;
use App\Car\Model\VersionModel;

use App\Car\Model\FeedbackModel;



use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

class HomeController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'home';

    }

    function test(){

        View::hamlReader('home','Admin');
    }

    function upd(){

        View::hamlReader('home/upd','Admin');
    }


    function index(){

        View::hamlReader('home','Admin');
    }

    function admin_h5_get(H5Model $model,$id){

        $m = Gets::getSingleInstance($model,$id);

        # 权限
        $m->checkPermission(19);

        # 允许操作接口
        $m->setOpt('get','../home/admin_h5_get');
        $m->setOpt('upd','../home/admin_h5_upd');
        $m->setOpt('view','home/upd');

        # 设置表体
        $m->setBody(['type'=>'hidden','name'=>'id']);
        $m->setBody(['title'=>'内容','name'=>'content','type'=>'h5']);

        # 设置名字
        $m->setName($m->getInfo()->title);
        
        # 输出
        $m->output();
            
    }
    function admin_h5_upd(H5Model $model,$id){
        $this->L->adminPermissionCheck(19);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $app = $model->find($id);
        !$app && AJAX::error('error');


        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }


    # 版本控制
    function version(){

        View::addData(['getList'=>'admin_'.__FUNCTION__]);
        View::hamlReader('home/list','Admin');
    }
    function admin_version(VersionModel $model,$page = 1,$limit = 10,$search,$car_number){
        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        $m->checkPermission(24);
        # 允许操作接口
        $m->setOpt('get','../'.$this->controller.'/'.__FUNCTION__.'_get');
        $m->setOpt('upd','../'.$this->controller.'/'.__FUNCTION__.'_upd');
        $m->setOpt('del','../'.$this->controller.'/'.__FUNCTION__.'_del');
        $m->setOpt('view','home/upd');
        $m->setOpt('add','home/upd');
        # 设置名字
        $m->setName('版本');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('版本号');
        $m->setHead('描述');
        $m->setHead('发布时间');
        $m->setHead('apk');
        $m->setHead('强制更新');
       
        # 设置表体
        $m->setBody('id');
        $m->setBody('version');
        $m->setBody('content');
        $m->setBody('create_date');
        $m->setBody(['name'=>'down','href'=>true]);
        $m->setBody(['name'=>'hard','type'=>'checkbox']);

        
        # 筛选
        $m->where = [];
        # 获取列表
        $model->order('create_time desc');
        $m->getList(0);
        $m->each(function(&$v){
            $v->create_date = date('Y-m-d H:i:s',$v->create_time);
            $v->down = '下载';
            $v->down_href = Func::fullAddr('download/file?id='.$v->file_id);
        });
        $m->output();

    }
    function admin_version_get(VersionModel $model,$id){

        $m = Gets::getSingleInstance($model,$id);
        # 权限
        $m->checkPermission(24);
        # 允许操作接口
        $m->setOpt('upd','../'.$this->controller.'/'.preg_replace('/get$/','upd',__FUNCTION__));
        $m->setOpt('back',$this->controller.'/'.preg_replace('/^admin_|_get$/','',__FUNCTION__));
        $m->setOpt('view','home/upd');
        # 设置表体
        $m->setBody(['type'   =>  'hidden','name'  =>  'id']);
        $m->setBody(['title'  =>  '版本号','name'  =>  'version','size'  =>  '4']);
        $m->setBody(['title'  =>  '描述','name'  =>  'content','size'  =>  '4']);
        $m->setBody(['title'  =>  'apk文件','name'  =>  'file_id','type'  =>  'file']);
        $m->setBody(['title'  =>  '强制更新','name'  =>  'hard','type'=>'radio','option'=>['0'=>'否','1'=>'是'],'default'=>'0']);
        # 设置名字
        $m->setName('版本控制');
        $m->getInfo();
        $m->output();
    }
    function admin_version_upd(VersionModel $model,$id,$pwd,$active,$birth){

        $this->L->adminPermissionCheck(24);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);
        if(!$id)$data['create_time'] = TIME_NOW;

        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_version_del(VersionModel $model,$id){
        $this->L->adminPermissionCheck(24);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

    function user_feedback(){

        View::addData(['getList'=>'admin_user_feedback']);
        View::hamlReader('home/list','Admin');
    }

    function admin_user_feedback(FeedbackModel $model,$limit=10,$page=1){
        
        $this->L->adminPermissionCheck(23);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../home/admin_user_feedback_get',
                'del'   => '../home/admin_user_feedback_del',
                'view'   => 'home/upd'
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '用户',
                '用户手机号',
                '反馈内容',
                '反馈时间'
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'phone',
                'content',
                'date'

            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->select('*','user.name','user.phone')->order('id')->where($where)->get()->toArray();
        foreach($list as &$v){
            $v->date = date('Y-m-d H:i:s',$v->create_time);
        }


        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_user_feedback_get(FeedbackModel $model,$id){
        
        $this->L->adminPermissionCheck(23);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../home/admin_user_feedback_get',
            'back'  => 'home/user_feedback',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '内容',
                'name'  =>  'content',
                'type'  =>  'textarea',
                'disabled'=>true

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
    function admin_user_feedback_upd(FeedbackModel $model,$id){
        $this->L->adminPermissionCheck(23);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_user_feedback_del(FeedbackModel $model,$id){
        $this->L->adminPermissionCheck(23);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }



}