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

use App\Car\Model\DriverCancelReasonModel;


class DriverController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        

    }

    function cancel_reason(){

        View::addData(['getList'=>'admin_cancel_reason']);
        View::hamlReader('home/list','Admin');
    }

    function admin_cancel_reason(DriverCancelReasonModel $model,$page=1,$limit=20){
        
        $this->L->adminPermissionCheck(158);

        $name = '取消理由';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../driver/admin_cancel_reason_get',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../driver/admin_cancel_reason_del',
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '理由',
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'msg',


            ];
            

        # 列表内容
        $where = [];
        

        $list = $model->order('id')->where($where)->get()->toArray();
        foreach($list as &$v){
            $v->thumb = Func::fullPicAddr($v->thumb);
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
    function admin_cancel_reason_get(DriverCancelReasonModel $model,$id){
        
        $this->L->adminPermissionCheck(158);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../driver/admin_cancel_reason_get',
            'upd'   => '../driver/admin_cancel_reason_upd',
            'back'  => 'driver/cancel_reason',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '理由',
                'name'  =>  'msg',

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
    function admin_cancel_reason_upd(DriverCancelReasonModel $model,$id){
        $this->L->adminPermissionCheck(158);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_cancel_reason_del(DriverCancelReasonModel $model,$id){
        $this->L->adminPermissionCheck(158);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }
    

}