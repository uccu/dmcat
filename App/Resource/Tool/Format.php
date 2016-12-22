<?php

namespace App\Resource\Tool;
use Lib\Sharp\SingleInstance;
use Config;

class Format implements SingleInstance{

    static function getInstance(){
        static $object;
		if(empty($object)) $object = new self();
		return $object;
    }






}