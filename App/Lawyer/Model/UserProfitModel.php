<?php

namespace App\Lawyer\Model;

use Model;


class UserProfitModel extends Model{

    public $table = 'user_profit';

    public function profit(){

        return $this->join(UserModel::class,'id','profit_id');
    }
}