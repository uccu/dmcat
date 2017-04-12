<?php

namespace App\School\Model;
use Model;

class UserModel extends Model{

    public $table = 'user';

    public $field;

    public function school(){

        return $this->join('App\School\Model\UserSchoolModel','user_id','id','LEFT');
        
    }

    public function classes(){

        return $this->join('App\School\Model\UserClassesModel','user_id','id','LEFT');
        
    }

    public function student(){

        return $this->join('App\School\Model\UserStudentModel','user_id','id','LEFT');
        
    }
    

    

}