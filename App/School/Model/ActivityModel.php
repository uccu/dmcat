<?php

namespace App\School\Model;
use Model;

class ActivityModel extends Model{

    public $table = 'activity';
    public $field;

    public function user(){

        return $this->join('App\School\Model\UserModel','id','user_id','LEFT');
    }
    

    

}