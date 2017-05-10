<?php

namespace App\School\Model;
use Model;

class StudentModel extends Model{

    public $table = 'student';
    public $field;

    public function classes(){

        return $this->join('App\School\Model\ClassesModel','id','classes_id','LEFT');
        
    }
    public function noticeConfirm(){

        return $this->join('App\School\Model\NoticeConfirmModel','student_id','id','LEFT');
        
    }
    
    

    

}