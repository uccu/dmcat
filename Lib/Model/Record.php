<?php

namespace Lib\Model;
use E;
use Model;
use Lib\Model\Using;

class Record{


    private $_null;
    private $_model;

    function __construct(array $data,$key = null){

        $this->_null = Using::getInstance()->model_null;

        if($key instanceof Model)$this->_model = $key;

        foreach($data as $k=>$v)$this->$k = $v;
        

    }

    function __get($k){

        return $this->_null ? null : '';

    }

    // function __set($k,$v){

    //     //E::throwEx('No Field');
    // }

    function __toString(){

        return json_encode($this);

    }

    function save(){

        if(!$this->_model)E::throwEx('Model Lost');

        $modelName = get_class( $this->_model );

        if($modelName=='Model'){
            $model = clone table($modelName,$this->_model->rawTable);
        }else $model = clone table($modelName);

        //var_dump($this->{$model->primary});
        return $model->set($this)->save($this->{$model->primary});
        
    }



}