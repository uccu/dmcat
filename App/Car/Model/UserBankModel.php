<?php

namespace App\Car\Model;
use Model;

class UserBankModel extends Model{

    public $table = 'user_bank';
    
    public function bank(){
        
        return $this->join(BankModel::class,'id','bank_id');
    }

}