<?php

namespace App\Car\Model;
use Model;

class DriverBankModel extends Model{

    public $table = 'driver_bank';
    public $field;
    public function bank(){
        
        return $this->join(BankModel::class,'id','bank_id','LEFT');
    }

}