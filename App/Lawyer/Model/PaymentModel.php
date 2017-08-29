<?php

namespace App\Lawyer\Model;

use Model;


class PaymentModel extends Model{

    public $table = 'payment';


    public function user(){
        
        return $this->join(UserModel::class,'id','user_id');
    }

    public function rule(){
        
        return $this->join(ConsultPayRuleModel::class,'id','rule_id');
    }

}