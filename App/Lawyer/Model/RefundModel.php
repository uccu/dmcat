<?php

namespace App\Lawyer\Model;

use Model;


class RefundModel extends Model{

    public $table = 'refund';
    public $field;
    public function user(){
        
        return $this->join(UserModel::class,'id','user_id');
    }
}