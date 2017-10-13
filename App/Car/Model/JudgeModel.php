<?php

namespace App\Car\Model;
use Model;

class JudgeModel extends Model{

    public $table = 'judge';

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
    }

}