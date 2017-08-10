<?php

namespace App\Lawyer\Model;

use Model;


class UserSchoolModel extends Model{

    public $table = 'user_school';
    public $field;

    public function school(){

        return $this->join(SchoolModel::class,'id','school_id');
    }

}