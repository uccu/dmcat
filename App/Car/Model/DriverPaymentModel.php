<?php

namespace App\Car\Model;
use Model;

class DriverPaymentModel extends Model{

    public $table = 'driver_payment';

    public function driver(){

        return $this->join(DriverModel::class,'id','driver_id');
    }

}