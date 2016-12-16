<?php

namespace Lib\Model;

use Lib\Model\Using;
use ArrayAccess;

class Container implements ArrayAccess{

    private $tool;

    private $data;

    private $status;

    function __construct($model,$key = null){

        $model->clean();
        
        $this->tool = Using::getInstance();

        $cmd = trim(strtoupper(substr($model->sql, 0, strpos($model->sql, ' '))));

        if ($cmd === 'UPDATE' || $cmd === 'DELETE' || $cmd === 'INSERT' || $cmd === 'REPLACE') {

            $this->status = $this->tool->query($model->sql);

            $this->data = array();

        } else{

            $this->data = $this->tool->fetch_all($model->sql,$key,$model);

            foreach($this->data as $k=>$v){

                $this->$k = $v;
            }

            $this->status = $this->data ? count($this->data) : 0;

        }
        
    }
    function offsetExists ($offset){

        return isset($this->data[$offset]);

    }
    function offsetGet ($offset) {

        return $this->data[$offset];
    }

    function offsetSet ($offset, $value) {

        return $this->data[$offset] = $value;
    }

    function offsetUnset ($offset) {

        unset($this->data[$offset]);
    }


    function getStatus(){

        return $this->status;
    }

    function __toString(){

        return json_encode($this->data);

    }

    function find($value,$key = null){

        if(!$key)return $this->data[$value] ? $this->data[$value] : null;

        foreach($this->data as $v){

            if($v[$key]==$value)return $v;

        }

        return null;

    }

    function get_field($field = null){

        
    }
    function toArray(){

        return $this->data;
    }



}