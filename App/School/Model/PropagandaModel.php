<?php

namespace App\School\Model;
use Model;

class PropagandaModel extends Model{

    public $table = 'propaganda';
    public $field;

    public function user(){

        return $this->join('App\School\Model\UserModel','id','user_id','LEFT');
    }
    

    

}