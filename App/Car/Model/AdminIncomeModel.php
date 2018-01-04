<?php

namespace App\Car\Model;
use Model;

class AdminIncomeModel extends Model{

    public $table = 'admin_income';

    public function admin(){

        return $this->join(AdminModel::class,'id','admin_id');
    }

    public function trip(){

        return $this->join(TripModel::class,'trip_id','trip_id','LEFT');
    }
}