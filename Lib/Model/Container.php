<?php

namespace Lib\Model;

use Lib\Model\Using;
use ArrayAccess;

class Container implements ArrayAccess{

    private $__tool;

    private $__data;

    private $__status;

    function __construct($model,$key = null){

        $model->clean();
        
        $this->__tool = Using::getSingleInstance();

        $cmd = trim(strtoupper(substr($model->sql, 0, strpos($model->sql, ' '))));

        if ($cmd === 'UPDATE' || $cmd === 'DELETE' || $cmd === 'INSERT' || $cmd === 'REPLACE') {

            $this->__status = $this->__tool->query($model->sql);

            $this->__data = array();

        } else{

            $this->__data = $this->__tool->fetch_all($model->sql,$key,$model);

            foreach($this->__data as $k=>$v){

                $this->$k = $v;
            }

            $this->__status = $this->__data ? count($this->__data) : 0;

        }
        
    }
    function offsetExists ($offset){

        return isset($this->__data[$offset]);

    }
    function offsetGet ($offset) {

        return $this->__data[$offset];
    }

    function offsetSet ($offset, $value) {
        
        $this->__data[$offset] = $value;
        $this->$offset = $value;

        return $value;
    }

    function offsetUnset ($offset) {

        unset($this->__data[$offset]);
        unset($this->$offset);
    }


    function getStatus(){

        return $this->__status;
    }

    function __toString(){

        return json_encode($this->__data);

    }

    function find($value,$key = null){

        if(!$key)return $this->__data[$value] ? $this->__data[$value] : null;

        foreach($this->__data as $v){

            if($v[$key]==$value)return $v;

        }

        return null;

    }

    function get_field($field = null){

        
    }
    function toArray(){

        return $this->__data;
    }



}