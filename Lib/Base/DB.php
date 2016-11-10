<?php


class DB{

    static function start(){

        return table('Lib/Model/Using')->start();
    }

    static function commit(){

        return table('Lib/Model/Using')->commit();
    }

    static function rollback(){

        return table('Lib/Model/Using')->rollback();
    }

    




}