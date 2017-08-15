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
use App\Lawyer\Model\SchoolModel;
use App\Lawyer\Model\UserSchoolModel;


class SchoolController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }

    function admin_school(SchoolModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(88);

        $name = '管理员';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/school/admin_school_get',
                'upd'   => '/school/admin_school_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/school/admin_school_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '名字',

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
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->pic ? Func::fullPicAddr($v->pic) : Func::fullPicAddr('nopic.jpg');
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
    function admin_school_get(SchoolModel $model,$id){

        $this->L->adminPermissionCheck(88);

        $name = '学校管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/school/admin_school_get',
                'upd'   => '/school/admin_school_upd',
                'back'  => 'school/school',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/school/admin_school_del',

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
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'pic',
                    'type'  =>  'pic',
                ],
                [
                    'title' =>  '简介',
                    'name'  =>  'description',
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
    function admin_school_upd(SchoolModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(88);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_school_del(SchoolModel $model,$id){
        $this->L->adminPermissionCheck(88);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




    function admin_user_school(UserSchoolModel $model,$page = 1,$limit = 10,$id){
        
        $this->L->adminPermissionCheck(86);

        $name = '用户学校';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/school/admin_user_school_get?type='.$id,
                'upd'   => '/school/admin_user_school_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/school/admin_user_school_del',

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '',
                '学校',
                '进度',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'name',
                'statusName',

            ];
            

        # 列表内容
        $where = [];

        $list = $model->select('school.name','school.id>school_id','school.pic','school.description','id','progress')->where(['user_id'=>$id])->page($page,$limit)->get()->toArray();

        $statuss = ['材料准备','递交','等待','补材料','结果'];
        foreach($list as &$v){
            $v->fullPic = $v->pic ? Func::fullPicAddr($v->pic) : Func::fullPicAddr('nopic.jpg');
            $v->statusName = $statuss[$v->progress];
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
    function admin_user_school_get(UserSchoolModel $model,SchoolModel $smodel,$id,$type){

        $this->L->adminPermissionCheck(86);

        $name = '用户学校';
        $statuss = ['材料准备','递交','等待','补材料','结果'];

        # 允许操作接口
        $opt = 
            [
                'get'   => '/school/admin_user_school_get',
                'upd'   => '/school/admin_user_school_upd?type='.$type,
                'back'  => 'school/user_school?id='.$type,
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/school/admin_user_school_del',

            ];

        $school = $smodel->get_field('name','id')->toArray();


        $tbody = 
            [

                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '学校',
                    'name'  =>  'school_id',
                    'type'  =>  'select',
                    'option'=>  $school
                ],
                [
                    'title' =>  '状态',
                    'name'  =>  'progress',
                    'type'  =>  'select',
                    'option'=>  $statuss
                ],
                [
                    'title' =>  '材料',
                    'name'  =>  'file',
                    'type'  =>  'file',
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
    function admin_user_school_upd(UserSchoolModel $model,$id,$type){
        $this->L->adminPermissionCheck(86);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        $data['user_id'] = $type;
        unset($data['id']);

        
        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_user_school_del(UserSchoolModel $model,$id){
        $this->L->adminPermissionCheck(86);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


}