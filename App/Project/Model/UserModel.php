<?php

namespace App\Project\Model;

use Model;

class UserModel extends Model
{
    public $table = 'user';



    protected $field = array('id','nickname');


    public function lession2(){

        return $this->join(LessionModel::class,'uid','id');
        
    }

    

}
