<?php

namespace App\School\Model;
use Model;

class UserStudentModel extends Model{

    public $table = 'user_student';

    public $field;

    public function schoolInfo(){

        return $this->join('App\School\Model\StudentModel','id','student_id','LEFT');
        
    }
    

    

}