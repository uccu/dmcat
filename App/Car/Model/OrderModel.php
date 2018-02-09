<?php

namespace App\Car\Model;
use Model;

class OrderModel extends Model{

    public $field;
    public $table = 'order';

    public function parkingLot(){

        return $this->join(ParkingLotModel::class,'id','parking_lot_id','LEFT');
    }

    public function car(){

        return $this->join(CarNumberModel::class,'id','car_number_id','LEFT');
    }

    public function feedback(){

        return $this->join(OrderFeedbackModel::class,'order_id','id','LEFT');
    }

}