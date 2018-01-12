<?php

namespace App\Car\Model;
use Model;

class AreaModel extends Model{

    public $table = 'area';

    public function parent(){

        return $this->join(AreaCopyModel::class,'id','parent_id');
    }

    public function child(){

        return $this->join(AreaCopyModel::class,'parent_id','id');
    }

}