<?php

namespace Lib\Model;

class PdoRecord{

    private $_model;

    function __construct($model){

        $this->_model = $model;

    }

}