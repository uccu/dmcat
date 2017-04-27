<?php

namespace App\School\Model;
use Model;

class SchoolMessageModel extends Model{

    public $table = 'school_message';


    public function user(){

        return $this->join('App\School\Model\UserModel','id','user_id');
        
    }
    

    

}