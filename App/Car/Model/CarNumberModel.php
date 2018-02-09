<?php

namespace App\Car\Model;
use Model;

class CarNumberModel extends Model{

    public $table = 'car_number';

    public function user(){

        return $this->join(UserModel::class,'id','user_id','LEFT');
    }

}