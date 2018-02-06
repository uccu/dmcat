<?php

namespace App\Car\Model;
use Model;

class OrderModel extends Model{

    public $table = 'order';

    public function parkingLot(){

        return $this->join(ParkingLotModel::class,'id','parking_lot_id','LEFT');
    }

    public function car(){

        return $this->join(CarNumberModel::class,'id','car_number_id','LEFT');
    }

}