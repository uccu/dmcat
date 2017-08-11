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
use App\School\Model\AttendanceModel;
use App\School\Model\ClassesMessageModel;
use App\School\Model\NoticeModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use Model;
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
    /* 更新点评 */

    function upd_comment($id,$month,$day,$date,CommentModel $model){
        $id = Request::getInstance()->cookie('student_id');
        $data = Request::getInstance()->request($model->field);
        unset($data['id']);
        if($date){
            $time = strtotime($date);
            $data['month'] = $month = date('Ym',$time);
            $data['day'] = $day = date('d',$time);
        }


        unset($data['id']);


        $data['teacher_id'] = $this->L->id?$this->L->id:0;
        $data['student_id'] = $id;
        $where['student_id'] = $id;
        $where['month'] = $month;
        $where['day'] = $day;

        $las = $model->where($where)->set($data)->save();


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

    function get_parent_message($page = 1,$limit = 30){

        $id = $this->L->id;

        $classes_id = Model::getInstance('user_classes')->where(['user_id'=>$id])->find()->classes_id;

        // $list = $model->select('reply','student.name','student.name_en','reply_time')->where(['student.classes.user.user_id'=>$id,['reply_time>0']])->page($page,$limit)->order('reply_time desc')->get()->toArray();

        $list = ClassesMessageModel::getInstance()->select('*','student.name','student.name_en')
            ->where(['classes_id'=>$classes_id])->page($page,$limit)->order('create_time desc')->get()->toArray();

        foreach($list AS $v){
            $v->title = $v->name.'的家长/'.$v->name_en.'\'s parent';
            $v->date = date('m.d H:i',$v->create_time);
        }
        AJAX::success(['list'=>$list]);
    }

    function reply($id,$message){
        $user_id = $this->L->id;
        $e = ClassesMessageModel::getInstance()->find($id);
        if(!$e)AJAX::error('恢复失败/Reply Failed');
        ClassesMessageModel::getInstance()->set(['reply'=>$message,'reply_time'=>TIME_NOW])->save($id);

        $sd = Model::getInstance('user_student')->where(['student_id'=>$e->student_id])->get();
        foreach($sd as $i)
            Func::add_message($i->user_id,'老师回复了您的留言<br><small>The teacher has replied to your message</small><br><small>'.$e->message.'</small><br><small>回复/Reply：'.$message.'</small>');
        AJAX::success();

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

        $sss = Func::getSignature();

        $appId = $this->L->config->wc_appid;
        $timestamp = $sss['timestamp'];
        $nonceStr = $sss['noncestr'];
        $signature = $sss['sign'];

        $id = $this->L->id;
        if(!$id)header('Location:/home/login');
        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }

    function profile(){

        $id = $this->L->id;
        if(!$id)Response::getInstance()->r302('/home/login');
        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }

    function leave(){

        $id = $this->L->id;
        if(!$id)Response::getInstance()->r302('/home/login');
        include VIEW_ROOT.'App/Teacher/'.__FUNCTION__.'.php';
    }

    function scan(StudentModel $studentModel,UserClassesModel $userClassesModel,AttendanceModel $attendanceModel){

        $sss = Func::getSignature();

        $data['appId'] = $appId = $this->L->config->wc_appid;
        $data['timestamp'] = $timestamp = $sss['timestamp'];
        $data['nonceStr'] = $nonceStr = $sss['noncestr'];
        $data['signature'] = $signature = $sss['sign'];

        $id = $this->L->id;
        if(!$id)Response::getInstance()->r302('/home/login');

        $classes_id = $userClassesModel->where(['user_id'=>$id])->find()->classes_id;
        if(!$classes_id)die('没有管理的班级！');

        $studentModel->where(['classes_id'=>$classes_id])->get()->toArray();

        $where['student.classes_id'] = $classes_id;
        $where['month'] = date('Ym');
        $where['day'] = date('d');
        $list = $attendanceModel->select('*','student.name','student.name_en')->where($where)->order('attend_time desc')->get()->toArray();

        $data['id'] = $id;
        $data['date'] = date('Y-m-d');
        $data['list'] = $list;
        
        View::addData($data);
        View::hamlReader('Teacher/'.__FUNCTION__,'App');
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
    function album(){

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
    

    # 老师发送通知
    function sendNotice(
        $title,
        $content,
        $short_message,
        $student_id,
        NoticeModel $noticeModel,
        UserClassesModel $userClassesModel,
        StudentModel $studentModel
        ){
        $id = $this->L->id;
        if(!$id)AJAX::error('not login');

        if($student_id){

            $data['title'] = $title;
            $data['short_message'] = $short_message;
            $data['content'] = $content;
            $data['create_time'] = TIME_NOW;
            $data['student_id'] = $student_id;
            $data['user_id'] =$this->L->id;
            $data['type'] = 1;
            $out['l'] = $noticeModel->set($data)->add()->getStatus();

        }else{
            $classes_id = $userClassesModel->where(['user_id'=>$id])->find()->classes_id;
            if(!$classes_id)AJAX::error('没有管理的班级！');
            $data['title'] = $title;
            $data['short_message'] = $short_message;
            $data['content'] = $content;
            $data['create_time'] = TIME_NOW;
            $data['classes_id'] = $classes_id;
            $data['user_id'] =$this->L->id;
            $data['type'] = 2;
            $out['l'] = $noticeModel->set($data)->add()->getStatus();

        }

        
        AJAX::success($out);


    }


    # 老师历史通知
    function MyClassesNotice(NoticeModel $noticeModel){
        
        $id = $this->L->id;
        if(!$id)AJAX::error('not login');

        $where['user_id'] = $this->L->id;
        $where['type'] = 2;
        $list = $noticeModel->where($where)->order('create_time','DESC')->get()->toArray();

        $out['list'] = $list;

        AJXA::success($out);

    }

    # 老师历史通知
    function MyStudentNotice(NoticeModel $noticeModel){
        
        $id = $this->L->id;
        if(!$id)AJAX::error('not login');

        $where['user_id'] = $this->L->id;
        $where['type'] = 1;
        $list = $noticeModel->where($where)->order('create_time','DESC')->get()->toArray();

        $out['list'] = $list;

        AJXA::success($out);

    }


    function post(){

        View::hamlReader('Teacher/'.__FUNCTION__,'App');
    }
    function send(UserClassesModel $userClassesModel,StudentModel $studentModel){
        $id = $this->L->id;
        if(!$id)header('Location:/home/login');
        $classes_id = $userClassesModel->where(['user_id'=>$id])->find()->classes_id;

        $student = $studentModel->where(['classes_id'=>$classes_id])->get()->toArray();

        View::addData(['student'=>$student]);

        View::hamlReader('Teacher/'.__FUNCTION__,'App');
    }
    function history_student(NoticeModel $noticeModel){

        $where['user_id'] = $this->L->id;
        $where['type'] = 1;
        $list = $noticeModel->select('*','student.name','student.name_en')->where($where)->order('create_time','DESC')->get()->toArray();

        foreach($list as &$v){
            $v->date = date('Y-m-d',$v->create_time);
        }

        View::addData(['list'=>$list]);
        View::hamlReader('Teacher/'.__FUNCTION__,'App');
    }



    function history_all(NoticeModel $noticeModel){

        $id = $this->L->id;
        if(!$id)header('Location:/home/login');

        $where['user_id'] = $this->L->id;
        $where['type'] = 2;
        $list = $noticeModel->where($where)->order('create_time','DESC')->get()->toArray();

        foreach($list as &$v){
            $v->date = date('Y-m-d',$v->create_time);
        }

        View::addData(['list'=>$list]);

        View::hamlReader('Teacher/'.__FUNCTION__,'App');
    }

}