<?php

namespace App\Car\Model;
use Model;

class UserApplyModel extends Model{

    public $table = 'user_apply';

    public function user(){

        return $this->join(UserModel::class,'id','id');
    }

    public function city(){
        
        return $this->join(AreaModel::class,'id','city_id','LEFT');
    }

}