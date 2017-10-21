<?php

namespace App\Car\Model;
use Model;

class DriverFeedbackModel extends Model{

    public $table = 'driver_feedback';

    public function driver(){

        return $this->join(DriverModel::class,'id','driver_id','LEFT');
    }
}