<?php

namespace App\School\Model;
use Model;

class RecruitModel extends Model{

    public $table = 'recruit';
    public $field;

    public function user(){

        return $this->join('App\School\Model\UserModel','id','create_user');
        
    }
    

    

}