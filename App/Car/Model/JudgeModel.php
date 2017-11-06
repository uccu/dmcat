<?php

namespace App\Car\Model;
use Model;

class JudgeModel extends Model{

    public $table = 'judge';

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
    }

    public function driver(){

        return $this->join(DriverModel::class,'id','driver_id');
    }

    public function user_driver(){

        return $this->join(UserModel::class,'id','driver_id');
    }

}