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

    




}