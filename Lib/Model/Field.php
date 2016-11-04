<?php

namespace Lib\Model;
use E;

class Field{


    public $table = '';
    
    public $fullTable = '';

    public $name = '';

    public $fullName = '';

    function __construct($field,$model){
        
        //必须字符串类型
        if(!is_string($field))E::throw('Undefined Field\'s Name');

        //加载工具库
        $this->tool = table('Lib/Model/Using');

        
        $fields = explode('.',$field);

        $count = count($fields);
        
        if($count==1){
            $this->fullTable = $model->asRawTable ? $model->table.' AS '.$model->asRawTable : $model->table;
            $this->table = $model->asRawTable ? $model->asRawTable : $model->table;
            $field = end($fields);
            $this->model = $model;
            if(!$model->hasField($field))E::throw('Field `'.$field.'` Not Defined',2);
            $this->name = $this->tool->quote_field( $field );
        }else{
            


            for($i=0;$i<$count-1;$i++){

                $field = $fields[$i];
                $model = $model->$field;

            }

            $field = end($fields);
            $this->model = $model;
            $this->fullTable = $model->asRawTable ? $model->table.' AS '.$model->asRawTable : $model->table;
            $this->table = $model->asRawTable ? $model->asRawTable : $model->table;
            if(!$model->hasField($field))E::throw('Field `'.$field.'` Not Defined',2);
            $this->name = $this->tool->quote_field( $field );


        }

        //设置完整的字段名
        $this->fullName = $this->table.'.'.$this->name;


        


    }







}