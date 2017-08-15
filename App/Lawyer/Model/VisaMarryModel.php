<?php

namespace App\Lawyer\Model;

use Model;


class VisaMarryModel extends Model{

    public $table = 'visa_marry';
    public $field;
    public function user(){

        return $this->join(UserModel::class,'id','id');
    }

}