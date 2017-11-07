<?php

namespace App\Car\Model;
use Model;

class DriverModel extends Model{

    public $table = 'driver';

    public function city(){

        return $this->join(AreaModel::class,'id','city_id');
    }

    public function online(){

        return $this->join(DriverOnlineModel::class,'driver_id','id');
    }

}