<?php

namespace App\Car\Model;
use Model;

class DriverMoneyLogModel extends Model{

    public $table = 'driver_money_log';
    public $field;
    public function driver(){
        
        return $this->join(DriverModel::class,'id','driver_id');
    }

}