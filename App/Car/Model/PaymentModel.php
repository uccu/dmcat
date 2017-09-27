<?php

namespace App\Car\Model;
use Model;

class PaymentModel extends Model{

    public $table = 'payment';

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
    }

}