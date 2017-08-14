<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;

# Model
use App\Lawyer\Model\LawyerModel;
use App\Lawyer\Model\BannerModel;
use App\Lawyer\Model\H5Model;
use App\Lawyer\Model\FastQuestionModel;
use App\Lawyer\Model\ConsultModel;


class ChatController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }

    function admin_fast(FastQuestionModel $model,$page = 1,$limit = 10){
        
        $this->L->adminPermissionCheck(86);

        $name = '管理员';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/chat/admin_fast_get',
                'upd'   => '/chat/admin_fast_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/chat/admin_fast_del',

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '问题',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'content',

            ];
            

        # 列表内容
        $where = [];

        $list = $model->where($where)->page($page,$limit)->get()->toArray();

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
    function admin_fast_get(FastQuestionModel $model,$id){

        $this->L->adminPermissionCheck(86);

        $name = '问题管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/chat/admin_fast_get',
                'upd'   => '/chat/admin_fast_upd',
                'back'  => 'chat/fast',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/chat/admin_fast_del',

            ];
        $tbody = 
            [

                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '问题',
                    'name'  =>  'content',
                    'type'  =>  'textarea',
                ],
                
            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_fast_upd(FastQuestionModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(86);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);

        
        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_fast_del(FastQuestionModel $model,$id){
        $this->L->adminPermissionCheck(86);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function admin_user_chat(ConsultModel $consultModel,LawyerModel $model,$id){

        $this->L->adminPermissionCheck(68);

        $name = '管理员';
        # 允许操作接口
        $opt = 
            [

                'back'  => 'staff/user'
                
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '律师ID',
                '律师类型',
                '名字',
                '聊天记录'

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'lawyer_id',
                'typeName',
                'name',
                [
                    'name'=>'look',
                    'href'=>true,
                ],

            ];
            

        # 列表内容
        $where['user_id'] = $id;
        $consultModel->distinct();

        $lawyer_list = $consultModel->where($where)->get_field('lawyer_id')->toArray();
        
        $list = [];
        foreach($lawyer_list as $v){

            $list[] = $consultModel->select('content','create_time','lawyer_id','lawyer.name','lawyer.avatar','lawyer.type')->where($where)->where(['lawyer_id'=>$v])->order('create_time desc')->find();
        }


        $types = ['法律','留学转学','签证'];

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->typeName = $types[$v->type];
            $v->look = '<i class="fa fa-pencil text-navy"></i> 查看';
        }

        # 分页内容
        $page   = $page;
        $max    = count($list);
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
    
}