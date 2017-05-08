<?php

namespace App\School\Controller;


use App\School\Model\StudentModel;
use App\School\Model\StudentPhysicalModel;
use App\School\Model\AttendanceModel;
use App\School\Model\RestDayModel;
use App\School\Model\CommentModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;
use App\School\Tool\Func;
use Model;

class StudentController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 学生档案列表 */
    function lists(StudentModel $model,$school_id,$classes_id,$page = 1,$limit = 50,$phy = 0){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/student/get','upd'=>'/student/upd','del'=>'/student/del'];
        if($phy)$out = ['get'=>'/student/physical_get','upd'=>'/student/physical_upd'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->student->student_name)=>['class'=>'tc'],
            ($this->lang->student->student_name).'(en)'=>['class'=>'tc'],
            ($this->lang->classes->class_name)=>['class'=>'tc'],
            ''=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            'class_name'=>['class'=>'tc'],
            '_pic'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $classesName = $this->lang->language == 'cn' ? 'classes.name>class_name' : 'classes.name_en>class_name';
        
        $out['lang'] = $this->lang->language;

        if($school_id)$model->where(['classes.school_id'=>$school_id]);
        if($classes_id)$model->where(['classes_id'=>$classes_id]);

        $list = $model->select('*', $classesName )->page($page,$limit)->get()->toArray();

        foreach($list as &$v){

            $v->_pic = '/student/qr?id='.$v->id;
        }

        if($school_id)$model->where(['classes.school_id'=>$school_id]);
        if($classes_id)$model->where(['classes_id'=>$classes_id]);
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;

        $out['list']  = $list;
        AJAX::success($out);


    }

    /* 体检 */
    function physical_get($id){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = Model::getInstance('student_physical')->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);


    }    

    /* 获取单个学生档案 */
    function get($id,StudentModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');

        $info->fullAvatar = $info->avatar?Func::fullPicAddr( $info->avatar ):'';

        $out['info'] = $info;

        AJAX::success($out);

    }

    /* 更新学生档案 */
    function upd($id,StudentModel $model){

        $data = Request::getInstance()->request($model->field);
        unset ($data['id']);

        if(!$id){
            $data['rand_code'] = TIME_NOW.Func::randWord('10',3);
            $data['create_time'] = TIME_NOW;
            $data['avatar'] = 'noavatar.png';
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }

    /* 更新体检信息 */
    function physical_upd($id,StudentPhysicalModel $model){

        $data = Request::getInstance()->request($model->field);
        $model->set($data)->save($id);
        AJAX::success();

    }

    /* 删除学生档案 */
    function del($id,StudentModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        Model::getInstance('student_physical')->remove($id);
        AJAX::success();

    }

    /* 获取出勤 */
    function attendance_get($month,AttendanceModel $attendanceModel,$student_id,$day){

        if(!$month || !$student_id)AJAX::error_i18n('param_error');

        $where['student_id'] = $student_id;
        $where['month'] = $month;
        $where['day'] = $day;

        $attendance = $attendanceModel->where($where)->find();

        if(!$attendance){

            $attendance = $where;
        }

        AJAX::success(['info'=>$attendance]);


    }

    /* 修改出勤 */
    function attendance_upd($month,$classes_id,AttendanceModel $attendanceModel,$student_id,$day,$status,$attend_time='',$reason=''){

        if(!$month || !$student_id)AJAX::error_i18n('param_error');

        $where['student_id'] = $student_id;
        $where['month'] = $month;
        $where['day'] = $day;

        $attendance = $attendanceModel->where($where)->find();

        if(!$attendance){

            $where['create_time'] = $where['update_time'] = TIME_NOW;
            $where['attend_time'] = $attend_time;
            $where['status'] = !is_null($status)?1:0;
            $where['reason'] = $reason;

            $attendanceModel->set($where)->add();
        }else{

            if(!$reason)$attendanceModel->remove($attendance->id);
            else{
                $where['update_time'] = TIME_NOW;
                $where['attend_time'] = $attend_time;
                $where['status'] = !is_null($status)?1:0;
                $where['reason'] = $reason;

                $attendanceModel->set($where)->save($attendance->id);
            }
            

        }

        AJAX::success();


    }

    /* 出勤列表 */
    function attendance_list($month,$classes_id,AttendanceModel $attendanceModel,StudentModel $studentModel,RestDayModel $restDayModel){

        if(!$month || !$classes_id)AJAX::error_i18n('param_error');
        
        
        $dayOfThisMonth = Func::calculateDayCount($month);

        $out = ['get'=>'/student/attendance_get','upd'=>'/student/attendance_upd','del'=>'/student/del'];

        $out['thead'][] = ['class'=>'tc','name'=>$this->lang->student->student];
        for($i=1;$i<=$dayOfThisMonth;$i++){
            $out['thead'][$i] = ['class'=>'tc','name'=>$i];
        }
        

        $where['month'] = $month;
        $where['student.classes_id'] = $classes_id;
        $out[] = $list = $attendanceModel->select(['student_id,GROUP_CONCAT(day) AS `day`,GROUP_CONCAT(status) AS `status`'],'RAW')->where($where)->group('student_id')->get('student_id')->toArray();
        foreach($list as &$v){
            $status = explode(',',$v->status);
            $day = explode(',',$v->day);
            $stat = [];
            foreach($status as $k=>$v2){
                $stat[$day[$k]] = $v2;
            }
            $v->stat = $stat;
        }

        $where2['classes_id'] = $classes_id;
        $name = $this->lang->language == 'cn' ?'name':'name_en';
        $out[] = $stu = $studentModel->where($where2)->get_field($name,'id');

        $body = [];
        $body['0'] = ['class'=>'tc'];
        for($i=1;$i<=$dayOfThisMonth; $body[$i++] = ['class'=>'tc changeIt cp t']);
        $out['tbody'] = $body;

        $restDay = $restDayModel->where(['month'=>$month])->get_field('day')->toArray();

        foreach($stu as $k=>$s){

            

            $body = [];
            $body['0'] = $s;
            $body['id'] = $k;
            for($i=1;$i<=$dayOfThisMonth; $i++){
                if($i<10)$i2 = '0'.$i;else $i2 = $i;
                if($month.$i2>DATE_TODAY)$body[$i] = '';
                elseif(isset($list[$k]->stat[$i2]) && $list[$k]->stat[$i2] == 1)$body[$i] = '√';
                elseif(isset($list[$k]->stat[$i2]) && $list[$k]->stat[$i2] == 0)$body[$i] = '❂';
                elseif(in_array($i2,$restDay))$body[$i] = '';
                elseif(!isset($list[$k]->stat[$i2]))$body[$i] = '×';
                
            };

            $out['list'][] = $body;
        }

        $out['test'] = $listSuccess;

        AJAX::success($out);
    }

    /* 出勤 */
    function attend($id,AttendanceModel $model){

        !$id && AJAX::error('Student Not Exist!');
        $data['student_id'] = $id;
        $data['month'] = date('Ym');
        $data['day'] = date('d');
        
        if($stu = $model->where( $data)->find()){

            if($stu->status == 1)AJAX::error('已经到校/has attended');

            $model->where( $data)->set(['attend_time'=>date('H:i:s'),'update_time'=>TIME_NOW])->save();
        }else{

            $data['create_time'] = $data['update_time'] = TIME_NOW;
            $data['attend_time'] = date('H:i:s');

            $model->set($data)->add();
        }

        
        AJAX::success();
    }

    /* 离开 */
    function leave($id,AttendanceModel $model){

        !$id && AJAX::error('Student Not Exist!');
        $where['student_id'] = $id;
        $where['month'] = date('Ym');
        $where['day'] = date('d');
        
        !($z = $model->where($where)->find()) && AJAX::error('Not Attended');
        $z->leave_time && AJAX::error('Has Left');

        $data['update_time'] = TIME_NOW;
        $data['leave_time'] = date('H:i:s');

        $model->set($data)->where($where)->save();
        AJAX::success();
    }

    /* 获取点评列表 */
    function comment_list($year,$month,$classes_id,CommentModel $commentModel,StudentModel $studentModel,RestDayModel $restDayModel){

        if(!$month || !$year || !$classes_id)AJAX::error_i18n('param_error');
        
        
        $dayOfThisMonth = Func::calculateDayCount($year.$month);

        $out = ['get'=>'/student/view_comment','upd'=>'/student/comment_upd','del'=>'/student/comment_del'];

        $out['thead'][] = ['class'=>'tc','name'=>$this->lang->student->student];
        for($i=1;$i<=$dayOfThisMonth;$i++){
            $out['thead'][$i] = ['class'=>'tc','name'=>$i];
        }
        

        $where['month'] = $year.$month;
        $where['student.classes_id'] = $classes_id;
        $out[] = $list = $commentModel->select(['student_id,GROUP_CONCAT(day) AS `day`'],'RAW')->where($where)->group('student_id')->get('student_id')->toArray();
        foreach($list as &$v){
            $v->stat= explode(',',$v->day);
            
        }

        $where2['classes_id'] = $classes_id;
        $name = $this->lang->language == 'cn' ?'name':'name_en';
        $out[] = $stu = $studentModel->where($where2)->get_field($name,'id');

        $body = [];
        $body['0'] = ['class'=>'tc'];
        for($i=1;$i<=$dayOfThisMonth; $body[$i++] = ['class'=>'tc changeIt cp t']);
        $out['tbody'] = $body;

        $restDay = $restDayModel->where(['month'=>$year.$month])->get_field('day')->toArray();

        foreach($stu as $k=>$s){

            

            $body = [];
            $body['0'] = $s;
            $body['id'] = $k;
            for($i=1;$i<=$dayOfThisMonth; $i++){
                if($i<10)$i2 = '0'.$i;else $i2 = $i;
                if($list[$k]->stat && in_array($i2,$list[$k]->stat))$body[$i] = '√';
                elseif($year.$month.$i2>DATE_TODAY)$body[$i] = '';
                elseif(in_array($i2,$restDay))$body[$i] = '';
                else $body[$i] = '×';
                
            };

            $out['list'][] = $body;
        }

        $out['test'] = $listSuccess;

        AJAX::success($out);
    }

    /* 更新点评 */
    function comment_upd($month,$student_id,$day,CommentModel $commentModel){

        if(!$month || !$student_id || !$day)AJAX::error_i18n('param_error');

        $where['student_id'] = $student_id;
        $where['month'] = $month;
        $where['day'] = $day;

        $comment = $commentModel->where($where)->find();

        $data = Request::getInstance()->request($commentModel->field);

        $data['learning'] = $data['learning']?$data['learning']:0;
        $data['eat'] = $data['eat']?$data['eat']:0;
        $data['life'] = $data['life']?$data['life']:0;

        if(!$comment){

            $data['create_time'] = TIME_NOW;
            $data['teacher_id'] = $this->L->id;
            $commentModel->set($data)->add();
        }else{

            $commentModel->set($data)->save($comment->id);

        }

        AJAX::success();


        
    }

    /* 删除点评 */
    function comment_del($month,$student_id,$day,CommentModel $commentModel){

        if(!$month || !$student_id || !$day)AJAX::error_i18n('param_error');

        $where['student_id'] = $student_id;
        $where['month'] = $month;
        $where['day'] = $day;

        $commentModel->where($where)->remove();

        AJAX::success();

    }

    /* 获取某次点评 */
    function view_comment($id,$month,$day,$date,CommentModel $model){

        if($date){
            $time = strtotime($date);
            $month = date('Ym',$time);
            $day = date('d',$time);
        }

        if(!$month || !$day)$info = $model->select('*','student.name','student.name_en','student.avatar')->where(['student_id'=>$id])->order('month DESC','day DESC')->find();
        else 
        $info = $model->select('*','student.name','student.name_en','student.avatar')->where(['student_id'=>$id,'month'=>$month,'day'=>$day])->find();

        !$info && AJAX::success(['info'=>[]]);

        $month = $info->month;
        $day = $info->day;

        $info->fullAvatar = Func::fullPicAddr( $info->avatar );
        
        $info->picArray = [];
        if($info->pic){
            $pics = explode(';',$info->pic);
            $info->pic2Array = $pics;
            foreach($pics as &$v)$v = Func::fullPicAddr( $v );
            $info->picArray = $pics;
        }

        $info->date = substr($info->month,0,4).'-'.substr($info->month,4).'-'.$info->day;

        $info->subdate = Func::subdate($month.$day);
        $info->adddate = Func::adddate($month.$day);

        $out['info'] = $info;

        
        

        AJAX::success($out);

    }

    /* 家长恢回复 */
    function reply($id = 0,$reply,CommentModel $model){

        $info = $model->find($id);
        !$info && AJAX::error('comment没有找到！');

        $info->reply = $reply;
        $info->save();
        AJAX::success();

    }

    /* 上传学生日常图片 */
    function upPic(){

        $out['path'] = Func::uploadFiles('file');
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);
    }
    
    /* 获取休息时间 */
    function restTime($year = 0,RestDayModel $model){

        $list = $model->where('%F BETWEEN %d AND %d','month',$year.'00',($year+1).'00')->get()->toArray();
        AJAX::success(['list'=>$list]);

    }

    /* 修改休息时间 */
    function change_restTime($month = 0,$day = 0,RestDayModel $model){

        $data['month'] = $month;
        $data['day'] = $day;

        $info = $model->where($data)->find();

        if(!$info)$model->set($data)->add();

        else $model->where($data)->remove();

        AJAX::success();

    }

    /* 请假 */
    function ask_leave($student_id = 0,$proposer,$date,$type,$content,AttendanceModel $model){

        $time  = strtotime($date);

        $ltime = strtotime( date('Ymd',$time) );

        if($ltime<TIME_NOW){

            AJAX::error('请提前8小时申请！/ Please apply for 8 hours in advance!');
        }

        !$student_id && AJAX::error('没有找到学生/student not find');
        
        $reason = '['.$type.']'.$proposer." : ".$content;

        $data['student_id'] = $student_id;
        $data['month'] = date('Ym',$time);
        $data['day'] = date('d',$time);
        
        $model->where($data)->find() && AJAX::error('已经请过假了/has asked');
    
        $data['create_time'] = $data['update_time'] = TIME_NOW;
        $data['status'] = 0;
        $data['reason'] = $reason;


        $model->set($data)->add();

        AJAX::success();
        
        
    }

    /* 学生二维码 */
    function qr($id = 0){

        Func::student_qr($id);
        
    }


}