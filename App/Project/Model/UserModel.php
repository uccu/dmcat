<?php

namespace App\Project\Model;
use Model;

class UserModel extends Model{

    public $table = 'user';







    public function user2(){

        static $model;
        if(empty($model))$model = new self;
        return $model;
        
    }

}