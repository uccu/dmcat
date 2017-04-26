<?php

namespace App\School\Model;
use Model;

class CommentModel extends Model{

    public $table = 'comment';
    public $field;

    public function student(){

        return $this->join('App\School\Model\StudentModel','id','student_id');
        
    }
    

    

}