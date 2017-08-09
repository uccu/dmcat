<?php

namespace App\Lawyer\Model;

use Model;


class ConsultModel extends Model{

    public $table = 'consult';

    public function lawyer(){

        return $this->join(LawyerModel::class,'id','lawyer_id');
    }

}