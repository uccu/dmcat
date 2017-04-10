<?php

namespace App\School\Controller;


use App\School\Model\StudentModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;

class StudentController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 学生列表 */
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

    function get($id,StudentModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

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


    function del($id,StudentModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }


    


}