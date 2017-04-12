<?php

namespace App\School\Model;
use Model;

class UserStudentModel extends Model{

    public $table = 'user_student';

    public $field;

    public function studentInfo(){

        return $this->join('App\School\Model\StudentModel','id','student_id','LEFT');
        
    }
    
    
    public function getUser($user_id){

        return $this->where(['user_id'=>$user_id])->get();

    }

    public function getStudent($student,$user_id){

        return $this->where(['user_id'=>$user_id,'student_id'=>$student])->find();
    }

    public function addStudent($student,$user_id){

        return $this->set(['user_id'=>$user_id,'student_id'=>$student])->add();
    }
    

}