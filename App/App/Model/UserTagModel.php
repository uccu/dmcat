<?php

namespace App\App\Model;
use Model;

class UserTagModel extends Model{

    public $table = 'user_tag';

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
    }
    public function tag(){

        return $this->join(TagModel::class,'id','tag_id');
    }
}