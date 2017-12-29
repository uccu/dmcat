<?php

namespace App\Car\Model;
use Model;

class DriverIncomeModel extends Model{

    public $table = 'driver_income';

    public function driver(){

        return $this->join(DriverModel::class,'id','driver_id');
    }

    public function trip(){

        return $this->join(TripModel::class,'trip_id','trip_id','LEFT');
    }
}