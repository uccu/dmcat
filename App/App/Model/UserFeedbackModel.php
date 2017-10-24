<?php

namespace App\App\Model;
use Model;

class UserFeedbackModel extends Model{

    public $table = 'user_feedback';

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
    }

}