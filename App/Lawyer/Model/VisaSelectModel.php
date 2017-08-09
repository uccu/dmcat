<?php

namespace App\Lawyer\Model;

use Model;


class VisaSelectModel extends Model{

    public $table = 'visa_select';
    public $field;

    public function option(){

        return $this->join(VisaSelectOptionModel::class,'select_id','id');
    }

}