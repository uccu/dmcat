<?php

namespace App\School\Controller;


use App\School\Model\ClassesModel;
use App\School\Model\ClassesLevelModel;
use App\School\Model\StudentModel;
use App\School\Model\UserModel;
use App\School\Model\ClassesMessageModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;
use App\School\Tool\Func;
use Model;

class ClassesController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 班级列表 */
    function lists(ClassesModel $model,$school_id){

        !$this->L->id && AJAX::errorn('未登录/no login');

        $this->L->check_type([3,5,6,7]);

        if($this->L->userInfo->type == 3){

            $classes_id = UserModel::getInstance()->select('classes.classes_id')->find($this->L->id)->classes_id;

            $model->where(['id'=>$classes_id]);
        }

        $out = ['get'=>'/classes/get','upd'=>'/classes/upd','del'=>'/classes/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->classes->class_name)=>['class'=>'tc'],
            ($this->lang->classes->class_name_en)=>['class'=>'tc'],
            ($this->lang->school->school_name)=>['class'=>'tc'],
            
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            'school_name'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $schoolName = $this->lang->language == 'cn' ? 'school.name>school_name' : 'school.name_en>school_name';
        
        $out['lang'] = $this->lang->language;

        if($school_id)$model->where(['school_id'=>$school_id]);

        $list = $model->select('*', $schoolName ,'level.name>level_name','level.name_en>level_name_en' )->get()->toArray();

        foreach($list as &$v){
            $v->name = $v->level_name.','.$v->name;
            $v->name_en = $v->level_name_en.','.$v->name_en;
        }

        $out['list']  = $list;
        AJAX::success($out);


    }

    function get($id,ClassesModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

    function upd($id,ClassesModel $model){

        $data = Request::getInstance()->request(['name','name_en','school_id','level']);
        $data['school_id'] = 1;
        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }

    function del($id,ClassesModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }


    function level_del($id,ClassesLevelModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }

    function level_upd($id,ClassesLevelModel $model){

        $data = Request::getInstance()->request(['name','name_en']);

        if(!$id){

            $model->set($data)->add();

        }else{

            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }

    function level_get($id,ClassesLevelModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }


    function level_lists(ClassesLevelModel $model,$school_id){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/classes/level_get','upd'=>'/classes/level_upd','del'=>'/classes/level_del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            '名字/chinese name'=>['class'=>'tc'],
            '英文名/english name'=>['class'=>'tc'],
            
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $list = $model->get()->toArray();


        $out['list']  = $list;
        AJAX::success($out);


    }


    function get_student_list($id = 0,StudentModel $model){

        $list = $model->select('id','avatar','pinyin','name','name_en')->where(['classes_id'=>$id])->order('pinyin')->get()->toArray();

        $listw = [];


        foreach($list as $v){

            if(!$v->name && $v->name_en)$v->name = $v->name_en;
            elseif(!$v->name && !$v->name_en)$v->name = 'no name';
            else $v->name = $v->name . ' ' . $v->name_en;

            $v->fullAvatar = Func::fullPicAddr($v->avatar);

            unset($v->name_en);

            if(!$v->pinyin){
                $listw[$v->pinyin][] = $v;
            }else{

                $first = strtoupper( substr($v->pinyin,0,1) );
                $listw[$first][] = $v;
            }
        }

        AJAX::success(['list'=>$listw]);



    }


    /* 给老师写信 */
    function add_classes_message($message = '',$student_id = 0){

        $this->L->check_type(1);

        if(!$message)AJAX::error_i18n('param_error');

        $stu = StudentModel::getInstance()->find($student_id);

        if(!$stu)AJAX::error('怀疑是这个假的学生/fake student!');

        $data['classes_id'] = $stu->classes_id;
        $data['student_id'] = $student_id;
        $data['message'] = $message;
        $data['create_time'] = TIME_NOW;

        Model::getInstance('classes_message')->set($data)->add();

        Func::add_message($this->L->id,'您已成功提交了一条老师留言<br><small>You have successfully submitted a school message</small><br><small>'.$message.'</small>');

        AJAX::success();

    }

    # 老师的信箱
    function message_lists(ClassesModel $model,$classes_id = 0){

        !$this->L->id && AJAX::errorn('未登录/no login');

        $this->L->check_type([3]);

        $out = ['get'=>'/classes/message_get','upd'=>'/classes/message_upd'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            '学生/Student'=>['class'=>'tc'],
            '日期/Date'=>['class'=>'tc'],
            '信息/Message'=>['class'=>'tc'],
            '回复/Reply'=>['class'=>'tc'],
            
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'date'=>['class'=>'tc'],
            'message'=>['class'=>'tc'],
            'reply'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $out['lang'] = $this->lang->language;


        $list = ClassesMessageModel::getInstance()->select('*','student.name','student.name_en')
            ->where(['classes_id'=>$classes_id])->order('create_time desc')->get()->toArray();

        foreach($list as &$v){
            $v->name = $v->name.','.$v->name_en;
            $v->date = date('Y.m.d',$v->create_time);
        }

        $out['list']  = $list;
        AJAX::success($out);


    }

    function message_get($id){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = ClassesMessageModel::getInstance()->find($id);
        !$info && AJAX::error_i18n('no_data');
        $info->reply && AJAX::error('你已经回复了该家长消息/You has replied this message!');

        AJAX::success($out);

    }

    function message_upd($id){

        $data = Request::getInstance()->request(['reply']);

        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            ClassesMessageModel::getInstance()->set($data)->add();

        }else{
            ClassesMessageModel::getInstance()->set($data)->save($id);
        }
        
        

        AJAX::success();

    }

}