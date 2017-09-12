<?php

namespace Lib\Model;
use Uccu\DmcatTool\Tool\E;
use Model;
use Lib\Model\Using;

class Record{


    private $_null;
    private $_model;

    function __construct(array $data,$key = null){

        $this->_null = Using::getSingleInstance()->model_null;

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
            $model = $modelName::copyMutiInstance($this->_model->rawTable);
        }else $model = $modelName::copyMutiInstance();
        return $model->set($this)->save($this->{$model->primary});
        
    }

    function remove(){

        if(!$this->_model)E::throwEx('Model Lost');

        $modelName = get_class( $this->_model );

        if($modelName=='Model'){
            $model = $modelName::copyMutiInstance($this->_model->rawTable);
        }else $model = $modelName::copyMutiInstance();

        return $model->remove($this->{$model->primary});
        
    }



}