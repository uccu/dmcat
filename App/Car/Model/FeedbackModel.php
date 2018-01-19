<?php

namespace App\Car\Model;
use Model;

class FeedbackModel extends Model{

    public $table = 'feedback';

    public function user(){

        return $this->join(UserModel::class,'id','user_id','LEFT');
    }

}