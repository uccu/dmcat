<?php

namespace Lib\Model;
use E;

class Field{

    public $tables = array();

    public $table = '';

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
            $this->tables[] = $model->table;
            $this->table = $model->table;
            $field = end($fields);
            if(!$model->hasField($field))E::throw('Field `'.$field.'` Not Defined',2);
            $this->name = $this->tool->quote_field( $field );
        }else{
            //设置字段名字
            $this->tables[] = $model->table;
            $field = end($fields);
            if(!$model->hasField($field))E::throw('Field `'.$field.'` Not Defined',2);
            $this->name = $this->tool->quote_field($field);

            //设置表名
            $m = $model->{prev($fields)}();
            $this->table = $this->tool->quote_table( $m->table );
            $this->tables[] = $this->table;

            //设置链式的表名
            while($table = prev($fields)){

                $m = $m->$table();
                $this->tables[] = $this->tool->quote_table( $m->table );
            }
            $this->tables = array_unique($this->tables);
        }

        //设置完整的字段名
        $this->fullName = $this->table.'.'.$this->name;


        


    }







}