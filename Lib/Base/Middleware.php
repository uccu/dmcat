<?php


use Lib\Sharp\SingleInstance;



class Middleware implements SingleInstance{

	

	public static function getInstance(){
        static $object;
		if(empty($object)) $object = table(get_called_class());
		return $object;
    }

	

	

}



?>