<?php

use Uccu\DmcatPdo\Model\BaseModel;

class Model extends BaseModel
{
    public $field;
    
    static function getInstance(...$con){
        return self::clone(...$con);
    }
}
