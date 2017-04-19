<?php

namespace App\School\Model;
use Model;

class AttendanceModel extends Model{

    public $table = 'attendance';
    public $field;

    public function student(){

        return $this->join('App\School\Model\StudentModel','id','student_id');
        
    }
    

    

}