<?php

namespace App\School\Model;
use Model;

class ClassesModel extends Model{

    public $table = 'classes';


    public function school(){

        return $this->join('App\School\Model\SchoolModel','id','school_id');
        
    }
    

    

}