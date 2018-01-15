<?php

namespace App\Car\Model;
use Model;

class AdminModel extends Model{

    public $table = 'admin';

    public function province(){

        return $this->join(AreaModel::class,'id','province_id','LEFT');
    }
    public function city(){

        return $this->join(AreaModel::class,'id','city_id','LEFT');
    }
    public function district(){

        return $this->join(AreaModel::class,'id','district_id','LEFT');
    }
    public function parkingLot(){

        return $this->join(ParkingLotModel::class,'id','parking_lot_id','LEFT');
    }

}