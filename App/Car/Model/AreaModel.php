<?php

namespace App\Car\Model;
use Model;

class AreaModel extends Model{

    public $table = 'area';

    public function area_t(){

        return $this->join(areaCopyModel::class,'id','parent_id');
    }

    public function area_b(){

        return $this->join(areaCopyModel::class,'parent_id','id');
    }

}