<?php

namespace App\Car\Model;
use Model;

class AreaCopy2Model extends Model{

    public $table = 'area';

    public function parent3(){

        return $this->join(AreaCopy3Model::class,'id','parent_id');
    }

    public function child3(){

        return $this->join(AreaCopy3Model::class,'parent_id','id');
    }

}