<?php

namespace App\Car\Model;
use Model;

class UserModel extends Model{

    public $table = 'user';
    
    public function city(){

        return $this->join(AreaModel::class,'id','city_id');
    }


}