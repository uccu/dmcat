<?php

namespace App\Car\Model;
use Model;

class ParkingLotModel extends Model{

    public $table = 'parking_lot';

    public function groups(){

        return $this->join(AreaModel::class,'id','group_id','LEFT');
    }

    public function district(){

        return $this->join(AreaModel::class,'id','district_id','LEFT');
    }

    public function admin(){

        return $this->join(AdminModel::class,'parking_lot_id','id','LEFT');
    }
}