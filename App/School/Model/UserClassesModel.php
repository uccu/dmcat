<?php

namespace App\School\Model;
use Model;

class UserClassesModel extends Model{

    public $table = 'user_classes';

    public $field;

    public function classesInfo(){

        return $this->join('App\School\Model\ClassesModel','id','classes_id','LEFT');
        
    }
    

    

}