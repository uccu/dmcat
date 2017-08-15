<?php

namespace App\Lawyer\Model;

use Model;


class VisaTechnologyModel extends Model{

    public $table = 'visa_technology';
    public $field;
    public function user(){

        return $this->join(UserModel::class,'id','id');
    }

}