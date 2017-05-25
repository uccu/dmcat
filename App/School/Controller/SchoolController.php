<?php

namespace App\School\Controller;


use App\School\Model\SchoolModel;
use App\School\Model\MessageModel;
use App\School\Model\UserModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Tool\Func;
use App\School\Middleware\L;
use Model;
class SchoolController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 学校列表 */
    function lists(SchoolModel $model){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/school/get','upd'=>'/school/upd','del'=>'/school/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->school->school_name)=>['class'=>'tc'],
            ($this->lang->school->school_name_en)=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        $out['lang'] = $this->lang->language;

        $list = $model->get()->toArray();

        $out['list']  = $list;
        AJAX::success($out);


    }

    /* 获取学校 */
    function get($id,SchoolModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

    /* 更新学校 */
    function upd($id,SchoolModel $model){

        $data = Request::getInstance()->request(['name','name_en']);

        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }

    /* 删除学校 */
    function del($id,SchoolModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }

    /* 添加学校通知 */
    function add_notice($id){

        $this->L->check_type([5,6,7]);
        $data = Request::getInstance()->request(['title','short_message','content','isshow']);

        $model = Model::getInstance('notice');
        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        AJAX::success();

    }

    /* 给校长写信（老师/行政） */
    function add_school_message($message){

        $this->L->check_type([3,5]);

        if(!$message)AJAX::error_i18n('param_error');

        $data['type'] = 2;
        $data['user_id'] = $this->L->id;
        $data['message'] = $message;
        $data['create_time'] = TIME_NOW;

        Model::getInstance('school_message')->set($data)->add();

        $ids = UserModel::getInstance()->where(['type'=>6])->get_field('id');

        Func::add_message($this->L->id,'您已成功提交了一条删除申请<br><small>You have successfully submitted a deletion request</small>');


        AJAX::success();

    }

    function message_info($id){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = Model::getInstance('school_message')->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);
    }


    function message_lists(){

        $out = ['get'=>'/school/message_info','upd'=>'/school/add_school_message'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            '留言/message'=>['class'=>'tc'],
            '留言时间/leave time'=>['class'=>'tc'],
            '已阅/has read'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'message'=>['class'=>'tc'],
            'time'=>['class'=>'tc'],
            'read'=>['class'=>'tc'],
        ];
        $out['lang'] = $this->lang->language;

        $list = Model::getInstance('school_message')->where(['user_id'=>$this->L->id])->order('id DESC')->get()->toArray();
        foreach($list as &$v){
            $v->time = date('y.m.d',$v->create_time);
            $v->read = $v->isread?'yes':'no';
        }

        $out['list']  = $list;
        AJAX::success($out);

    }


    


}