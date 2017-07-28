<?php

namespace App\Project\Model;
use Model;

class UserModel extends Model{

    public $table = 'user';


    public function friendsTable(){

        return $this->join(FriendsModel::class,'user_id','id');
    }
    

}