<?php

namespace App\Lawyer\Model;

use Model;


class VisaSendModel extends Model{

    public $table = 'visa_send';
    public $field;

    public function user(){
        
        return $this->join(UserModel::class,'id','user_id');
    }

}