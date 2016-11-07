<?php

namespace App\Project\Model;
use Model;

class UserModel extends Model{

    public $table = 'user';



    protected $field = ['id','name'];


    public function lession2(){

        return $this->join(LessionModel::class,'uid','id');
        
    }

    

}