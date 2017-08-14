<?php

namespace App\Lawyer\Model;

use Model;


class UserModel extends Model{

    public $table = 'user';
    public $field;

    public function lawyer(){

        return $this->join(LawyerModel::class,'id','lawyer_id','LEFT');
    }

}