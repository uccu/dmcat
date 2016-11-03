<?php

namespace App\Project\Model;
use Model;


class LessionModel extends Model{

    public $table = 'lession';


    public function user(){

        return $this->join(UserModel::class,'id','uid');
        
    }

}