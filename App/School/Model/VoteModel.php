<?php

namespace App\School\Model;
use Model;

class VoteModel extends Model{

    public $table = 'vote';
    public $field;

    public function user(){

        return $this->join('App\School\Model\UserModel','id','user_id','LEFT');
    }
    

    

}