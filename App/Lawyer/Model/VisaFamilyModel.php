<?php

namespace App\Lawyer\Model;

use Model;


class VisaFamilyModel extends Model{

    public $table = 'visa_family';
    public $field;
    public function user(){

        return $this->join(UserModel::class,'id','id');
    }

}