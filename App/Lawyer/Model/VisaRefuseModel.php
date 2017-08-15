<?php

namespace App\Lawyer\Model;

use Model;


class VisaRefuseModel extends Model{

    public $table = 'visa_refuse';
    public $field;
    public function user(){

        return $this->join(UserModel::class,'id','id');
    }

}