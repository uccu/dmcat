<?php

namespace App\Lawyer\Model;

use Model;


class VisaPerpetualModel extends Model{

    public $table = 'visa_perpetual';
    public $field;
    public function user(){

        return $this->join(UserModel::class,'id','id');
    }

}