<?php

namespace App\App\Model;
use Model;

class DoctorFeedbackModel extends Model{

    public $table = 'doctor_feedback';

    public function doctor(){

        return $this->join(DoctorModel::class,'id','doctor_id');
    }

}