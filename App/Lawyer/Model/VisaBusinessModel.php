<?php

namespace App\Lawyer\Model;

use Model;


class VisaBusinessModel extends Model{

    public $table = 'visa_business';
    public $field;

    public function user(){

        return $this->join(UserModel::class,'id','id');
    }
}