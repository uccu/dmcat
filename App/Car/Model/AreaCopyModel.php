<?php

namespace App\Car\Model;
use Model;

class AreaCopyModel extends Model{

    public $table = 'area';

    public function parent2(){

        return $this->join(AreaCopy2Model::class,'id','parent_id');
    }

    public function child2(){

        return $this->join(AreaCopy2Model::class,'parent_id','id');
    }

}