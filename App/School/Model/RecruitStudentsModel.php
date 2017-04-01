<?php

namespace App\School\Model;
use Model;

class RecruitStudentsModel extends Model{

    public $table = 'recruit_students';


    public function recruit(){

        return $this->join('App\School\Model\RecruitModel','id','recruit_id');
        
    }
    

    

}