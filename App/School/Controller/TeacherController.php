<?php

namespace App\School\Controller;


use Controller;
use Response;
use Request;
use App\School\Model\UserModel;
use App\School\Model\StudentModel;
use App\School\Model\CommentModel;
use App\School\Model\UserClassesModel;
use App\School\Model\MessageModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;

class TeacherController extends Controller{


    private $L;


    function __construct(){

        $this->L = L::getInstance();
        $this->salt = $this->L->config->site_salt;

    }

    /* 每日点评 */

    function add_comment($id,$month,$day,$date,CommentModel $model){
        $id = Request::getInstance()->cookie('student_id');
        $data = Request::getInstance()->request($model->field);
        unset($data['id']);
        if($date){
            $time = strtotime($date);
            $data['month'] = $month = date('Ym',$time);
            $data['day'] = $day = date('d',$time);
        }


        unset($data['id']);

        $data['create_time'] = TIME_NOW;
        $data['teacher_id'] = $this->L->id?$this->L->id:0;
        $data['student_id'] = $id;
        $where['student_id'] = $id;
        $where['month'] = $month;
        $where['day'] = $day;

        $las = $model->where($where)->find();

        if($las)AJAX::error('已评论/has commented');


        $model->set($data)->add();

        AJAX::success();

    }

    function get_my_info($id ,UserModel $model){

        $id = $this->L->id;
        
        !$id && AJAX::success(['info'=>[]]);
        $info = $model->select('type','avatar','raw_password>password','phone','email')->find($id);
        if(!$info)AJAX::error('用户不存在/Not Exist User');
        if($info->type != '3')AJAX::error('用户非老师/Not Teacher');

        
        if($info->avatar)$info->avatar = Func::fullPicAddr($info->avatar);
        
        $out['info'] = $info;

        !$info && AJAX::error_i18n('no_data');
        $out['lang'] = $this->lang->language;

        AJAX::success($out);

    }

    function upd($id = 0,UserModel $model){

        $id = $this->L->id;

        $data = Request::getInstance()->request(['email','phone','raw_password','avatar']);
        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_user_exist');
        if(!$data['avatar'])unset($data['avatar']);
        if(!$data['raw_password'])unset($data['raw_password']);
        elseif($data['raw_password'] && $info->raw_password !== $data['raw_password'])
            $data['password'] = sha1($this->salt.md5($data['raw_password']));

        $model->set($data)->save($id);
        

        AJAX::success();

    }

    function get_parent_message($page = 1,$limit = 30,CommentModel $model){

        $id = $this->L->id;

        $list = $model->select('reply','student.name','student.name_en','reply_time')->where(['student.classes.user.user_id'=>$id,['reply_time>0']])->page($page,$limit)->order('reply_time desc')->get()->toArray();

        foreach($list AS $v){
            $v->title = $v->name.'家长/'.$v->name_en.'\'s parent';
            $v->date = date('m.d H:i');
        }
        AJAX::success(['list'=>$list]);
    }

    function get_message($page = 1,$limit = 30,MessageModel $model){

        $id = $this->L->id;

        $list = $model->where(['user_id'=>$id])->page($page,$limit)->order('id desc')->get()->toArray();

        foreach($list AS $v){

            $v->date = date('m.d H:i');
        }

        $model->where(['user_id'=>$id])->set(['isread'=>1])->save();
        
        AJAX::success(['list'=>$list]);
    }

    function index(){

        $id = $this->L->id;
        if(!$id)header('Location:/home/login');
        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }

    function profile(){

        $id = $this->L->id;
        if(!$id)Response::getInstance()->r302('/home/login');
        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }

    function apply(){

        $id = $this->L->id;
        if(!$id)header('Location:/home/login');
        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }

    function record($id){

        if($id)Response::getInstance()->cookie('student_id',$id,0);

        $tid = $this->L->id;
        if(!$tid)header('Location:/home/login');
        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }

    function parent(){

        $id = $this->L->id;
        if(!$id)header('Location:/home/login');
        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }
    function message(){

        $id = $this->L->id;
        if(!$id)header('Location:/home/login');

        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }

    function lists(UserClassesModel $model){
        $id = $this->L->id;
        if(!$id)header('Location:/home/login');

        $class_id = $model->where(['user_id'=>$id])->find()->classes_id;

        View::addData(['id'=>$id,'class_id'=>$class_id]);
        View::addData(['lang'=>$lang]);
        View::hamlReader('Teacher/'.__FUNCTION__,'App');
    }


}