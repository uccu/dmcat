<?php

namespace App\Project\Model;
use Model;


class LessionModel extends Model{

    public $table = 'lession';


    protected $field = ['id','name','uid'];

    public function u(){

        return $this->join(UserModel::class,'id','uid');
        
    }


    public function Tteacher(){

        return $this->join(UserModel::class,'id','uid');

    }

}