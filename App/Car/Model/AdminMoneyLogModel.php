<?php

namespace App\Car\Model;
use Model;

class AdminMoneyLogModel extends Model{

    public $table = 'admin_money_log';
    public $field;
    public function admin(){
        
        return $this->join(AdminModel::class,'id','admin_id','LEFT');
    }
    public function adminBank(){
        
        return $this->join(AdminBankModel::class,'id','bank_id','LEFT');
    }

}