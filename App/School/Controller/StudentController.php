<?php

namespace App\School\Controller;


use App\School\Model\StudentModel;
use App\School\Model\AttendanceModel;
use App\School\Model\RestDayModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;
use App\School\Tool\Func;

class StudentController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 学生档案列表 */
    function lists(StudentModel $model,$school_id,$classes_id,$page = 1,$limit = 50){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/student/get','upd'=>'/student/upd','del'=>'/student/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->student->student_name)=>['class'=>'tc'],
            ($this->lang->student->student_name).'(en)'=>['class'=>'tc'],
            ($this->lang->classes->class_name)=>['class'=>'tc'],
            
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            'class_name'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $classesName = $this->lang->language == 'cn' ? 'classes.name>class_name' : 'classes.name_en>class_name';
        
        $out['lang'] = $this->lang->language;

        if($school_id)$model->where(['classes.school_id'=>$school_id]);
        if($classes_id)$model->where(['classes_id'=>$classes_id]);

        $list = $model->select('*', $classesName )->page($page,$limit)->get()->toArray();

        if($school_id)$model->where(['classes.school_id'=>$school_id]);
        if($classes_id)$model->where(['classes_id'=>$classes_id]);
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;

        $out['list']  = $list;
        AJAX::success($out);


    }

    /* 获取单个学生档案 */
    function get($id,StudentModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

    /* 更新学生档案 */
    function upd($id,StudentModel $model){

        $data = Request::getInstance()->request($model->field);
        unset ($data['id']);

        if(!$id){
            $data['rand_code'] = TIME_NOW.Func::randWord('10',3);
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }

    /* 删除学生档案 */
    function del($id,StudentModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }

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


    function attend($id,AttendanceModel $model){

        !$id && AJAX::error('Student Not Exist!');
        $data['student_id'] = $id;
        $data['month'] = date('Ym');
        $data['day'] = date('d');
        
        $model->where( $data)->find() && AJAX::error('Has Attended');

        $data['create_time'] = $data['update_time'] = TIME_NOW;
        $data['attend_time'] = date('H:i:s');

        $model->set($data)->add();
        AJAX::success();
    }

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




    


}