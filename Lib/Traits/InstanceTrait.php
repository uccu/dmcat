<?php
namespace Lib\Traits;

Trait InstanceTrait{

    public static function getSingleInstance(){

        static $object;
		if(empty($object)){

            $params = func_get_args();
            $object = new self(...$params);
        }
		return $object;
    }

}