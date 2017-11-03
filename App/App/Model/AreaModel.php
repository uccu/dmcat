<?php

namespace App\App\Model;
use Model;


class AreaModel extends Model{

    public $table = 'area';

    public function area_t(){

        return $this->join(AreaCopyModel::class,'id','parent_id');
    }

    public function area_b(){

        return $this->join(AreaCopyModel::class,'parent_id','id');
    }

}