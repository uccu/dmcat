<?php

namespace App\School\Model;
use Model;

class ClassesMessageModel extends Model{

    public $table = 'classes_message';
    public $field;

    public function student(){

        return $this->join('App\School\Model\StudentModel','id','student_id');
    }
    

    

}