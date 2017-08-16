<?php

namespace App\Lawyer\Model;

use Model;


class UserModel extends Model{

    public $table = 'user';
    public $field;

    public function lawyer(){
        
        return $this->join(LawyerModel::class,'id','lawyer_id','LEFT');
    }
    public function lim(){
        
        return $this->join(UserConsultLimitModel::class,'user_id','id','LEFT');
    }

}