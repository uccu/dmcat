<?php

namespace App\Lawyer\Model;

use Model;


class VisaTravelModel extends Model{

    public $table = 'visa_travel';
    public $field;

    public function user(){

        return $this->join(UserModel::class,'id','id');
    }
}