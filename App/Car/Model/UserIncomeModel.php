<?php

namespace App\Car\Model;
use Model;

class UserIncomeModel extends Model{

    public $table = 'user_income';

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
    }

}