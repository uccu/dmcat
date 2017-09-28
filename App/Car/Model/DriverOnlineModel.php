<?php

namespace App\Car\Model;
use Model;

class DriverOnlineModel extends Model{

    public $table = 'driver_online';

    public function driver(){

        return $this->join(DriverModel::class,'id','driver_id');
    }

}