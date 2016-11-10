<?php

namespace App\Project\Model;
use Model;


class LessionModel extends Model{

    public $table = 'lession';
    protected $updateSafe = false;

    protected $field = array('id','name','uid','uid'=>'uid2','COUNT(*)'=>'count');

    public function user(){

        return $this->join('App\\Project\\Model\\User','id','uid');
        
    }


    public function Tteacher(){

        return $this->join('App\\Project\\Model\\User','id','uid');

    }

}