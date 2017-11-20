<?php

namespace App\Car\Model;
use Model;

class UsedCarModel extends Model{

    public $table = 'used_car';

    public $field;

    public function city(){
        
        return $this->join(AreaModel::class,'id','city_id','LEFT');
    }

    public function user(){
        
        return $this->join(UserModel::class,'id','user_id','LEFT');
    }

}