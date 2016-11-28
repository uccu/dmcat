<?php

namespace Lib\Model;


class Container{

    private $tool;

    private $data;

    function __construct($model,$key = null){
        
        $this->tool = table('Lib/Model/Using');

        $cmd = trim(strtoupper(substr($model->sql, 0, strpos($model->sql, ' '))));

        if ($cmd === 'UPDATE' || $cmd === 'DELETE' || $cmd === 'INSERT' || $cmd === 'REPLACE') {

            $this->status = $this->tool->query($model->sql);

            $this->data = array();

        } else{

            $this->data = $this->tool->fetch_all($model->sql,$key,$model);

            $this->status = $this->data ? count($this->data) : 0;

        }
        
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
    



}