<?php

namespace App\Project\Model;
use Model;

class UserModel extends Model{

    public $table = 'user';



    protected $field = ['id','name'];


    public function l(){

        return $this->join(LessionModel::class,'uid','id');
        
    }

    

}