<?php

namespace App\School\Model;
use Model;

class StudentModel extends Model{

    public $table = 'student';
    public $field;

    public function classes(){

        return $this->join('App\School\Model\ClassesModel','id','classes_id','LEFT');
        
    }
    

    

}