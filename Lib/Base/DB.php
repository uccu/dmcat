<?php

use Lib\Model\Using;

class DB{

    static function start(){

        return Using::getSingleInstance()->start();
    }

    static function commit(){

        return Using::getSingleInstance()->commit();
    }

    static function rollback(){

        return Using::getSingleInstance()->rollback();
    }

    # 原生
    static function raw($str = ''){

        $obj = new self;
        $obj->type = 'raw';
        $obj->str = $str;
        return $obj;

    }




}