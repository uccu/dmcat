<?php

namespace App\Lawyer\Model;

use Model;


class VisaGraduateModel extends Model{

    public $table = 'visa_graduate';
    public $field;
    public function user(){

        return $this->join(UserModel::class,'id','id');
    }

}