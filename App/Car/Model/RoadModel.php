<?php

namespace App\Car\Model;
use Model;

class RoadModel extends Model{

    public $table = 'road';

    public $field;


    public function user(){
        
        return $this->join(UserModel::class,'id','user_id','LEFT');
    }

}