<?php

use Lib\Model\Using;

class DB{

    static function start(){

        return Using::getInstance()->start();
    }

    static function commit(){

        return Using::getInstance()->commit();
    }

    static function rollback(){

        return Using::getInstance()->rollback();
    }

    




}