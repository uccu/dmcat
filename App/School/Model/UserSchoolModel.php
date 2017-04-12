<?php

namespace App\School\Model;
use Model;

class UserSchoolModel extends Model{

    public $table = 'user_school';

    public $field;

    public function schoolInfo(){

        return $this->join('App\School\Model\SchoolModel','id','school_id','LEFT');
        
    }
    

    

}