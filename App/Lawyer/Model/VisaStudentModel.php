<?php

namespace App\Lawyer\Model;

use Model;


class VisaStudentModel extends Model{

    public $table = 'visa_student';
    public $field;
    public function user(){

        return $this->join(UserModel::class,'id','id');
    }

}