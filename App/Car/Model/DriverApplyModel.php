<?php

namespace App\Car\Model;
use Model;

class DriverApplyModel extends Model{

    public $table = 'driver_apply';

    public function driver(){

        return $this->join(DriverModel::class,'id','id');
    }

    public function city(){
        
        return $this->join(AreaModel::class,'id','city_id','LEFT');
    }

}