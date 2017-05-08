<?php

namespace App\School\Model;
use Model;

class NoticeModel extends Model{

    public $table = 'notice';
    public $field;


    public function user(){

        return $this->join('App\School\Model\UserModel','id','user_id','LEFT');
    }

    

}