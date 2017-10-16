<?php

namespace App\Car\Model;
use Model;

class UserMoneyLogModel extends Model{

    public $table = 'user_money_log';

    public function user(){
        
        return $this->join(UserModel::class,'id','user_id');
    }

}