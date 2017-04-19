<?php

namespace App\School\Controller;


use App\School\Model\StudentModel;
use App\School\Model\AttendanceModel;
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
            ($this->lang->student->student_name_en)=>['class'=>'tc'],
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

    /* 出勤列表 */
    function attendance_list($month,$classes_id,AttendanceModel $attendanceModel,StudentModel $studentModel){

        if(!$month || !$classes_id)AJAX::error_i18n('');
        
        
        $dayOfThisMonth = Func::calculateDayCount($month);


        // $out['thead'] = [
        //     ($this->lang->student->student)=>['class'=>'tc'],
        // ];
        // for($i=1;$i<=$dayOfThisMonth; $out['thead'][$i++] = ['class'=>'tc']);
        // $out['thead']['_opt'] = ['class'=>'tc'];

        $where['month'] = $month;
        $where['student.classes_id'] = $classes_id;
        $out[] = $list = $attendanceModel->select(['student_id,GROUP_CONCAT(day) AS `day`,GROUP_CONCAT(status) AS `status`'],'RAW')->where($where)->group('student_id')->get('student_id')->toArray();
        foreach($list as &$v){
            if($v->status)$v->status = explode(',',$v->status);
            if($v->day)$v->day = explode(',',$v->day);
        }

        $where2['classes_id'] = $classes_id;
        $name = $this->lang->language = 'cn' ?'name':'name_en';
        $out[] = $stu = $studentModel->where($where2)->get_field($name,'id');

        foreach($stu as $s){

            $body = [];
            


        }
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            'class_name'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        

        $out['test'] = $listSuccess;

        AJAX::success($out);
    }




    


}