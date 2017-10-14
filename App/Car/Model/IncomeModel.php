<?php

namespace App\Car\Model;
use Model;

class IncomeModel extends Model{

    public $table = 'income';

    public function trip(){

        return $this->join(TripModel::class,'id','trip_id','LEFT');
    }

}