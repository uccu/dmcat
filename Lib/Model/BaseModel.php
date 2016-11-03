<?php

namespace Lib\Model;


use Config;
use Lib\Model\Container;

class BaseModel{

    const BLOCK = ' ';

    public      $table;

    public      $rawTable;

    protected   $field;

    protected   $primary;

    private     $cmd;

    private     $select;

    private     $join;

    private     $on;

    private     $where;

    private     $group;

    private     $order;

    private     $offset;
    
    private     $limit;

    private     $set;
    
    private     $tool;

    public      $sql;


    function __construct($tableName = null){

        $mb = conf('Mysqli');

        $this->tool = table('Lib/Model/Using');

    
        if($tableName)$this->table = $tableName;

        $this->rawTable = $this->table;

        if($mb->PREFIX){
            
            $this->table = $mb->PREFIX.$this->table;

        }

        $this->table = $this->tool->quote_table($this->table);

        $this->_COLUMNS();

        if(is_string($this->field))$this->field = array($this->field);


        if(!$this->primary)$this->primary = $this->field[0];
        
        

    }


    private function _COLUMNS(){

        if(!$this->field){

            $this->field = $this->tool->fetch_all('SHOW FULL COLUMNS FROM '.$this->table);

            foreach($this->field as &$v)$v = reset($v);

        }

        return $this;

    }

    function get(){

        $this->cmd = 'SELECT ';

        $sql = $this->cmd;

        if(!$this->select)$this->select = '*';

        $sql .= $this->select.' FROM '.$this->table;

        if($this->on){

            $sql .= $this->on;


        }

        if($this->where){

            $sql .= ' WHERE '.$this->where;
        }


        if($this->group){


            $sql .= ' GROUP BY '.$this->group;
        }

        if($this->order){


            $sql .= ' ORDER BY '.$this->order;
        }

        

        if($this->limit){


            $sql .= ' LIMIT '.$this->limit;
        }

        if($this->offset){


            $sql .= ' OFFSET '.$this->offset;
        }

        $this->sql = $sql;

        return new Container($this);


    }


    function find($id = 0){

        $this->limit = 1;

        if($id){

            $field = new Field($this->primary,$this);
            $this->where =  $field->fullName.' = '.$this->tool->quote($id);

        }
        
        return $this->get(0);

    }




    function save(){





        $this->cmd = 'UPDATE';

    }

    function add(){


        $this->cmd = 'INSERT INTO';
    }

    function replace(){


        $this->cmd = 'REPLACE INTO';
    }

    

    

    



    function select(){

        $container = func_get_args();

        $fields = array();

        foreach($container as $v){

            if(is_string($v))$v = array($v);

            foreach($v as $j){

                $field = new Field($j,$this);

                //if($field)1;

                $fields[] = $field->fullName;
            }
            
        }

        if($fields)$this->select = implode(',',$fields);

        return $this;

    }

    function selectExcept(){

        


    }

    function offset($i = null){
        if(!$i)return $this;
        $i = floor($i);
        if($i<0)$i = 0;
        $this->offset = $i;
        return $this;
    }


    function limit($i = null){
        if(!$i)return $this;
        $i = floor($i);
        if($i<1)$i = 1;
        $this->limit = $i;
        return $this;
    }

    function page($page = 1,$count = null){
        
        $this->limit($count);
        
        $count = $this->limit;

        if($count){

            $page = floor($page);
            if($page<1)$page = 1;
            $offset = ($page-1)*$count;
            $this->offset($offset);

        }

        return $this;
    }

    
    

    function order($field,$order){
        

    }



    function __toString(){

        return (string)$this->sql;

    }



    function hasField($field){

        $this->_COLUMNS();

        return in_array($field,$this->field) ? true : false;
       
    }





    protected function join($class,$forign = null,$key = null,$join = 'INNER'){
         
        $c = table($class);

        if(!$this->forign[$class]){

            if(!$forign)$forign = $this->rawTable.'_id';

            if(!$key)$key = $this->primary;

            $forign = new Field($forign,$c);

            $key = new Field($key,$this);

            $this->forign[$class] = ' '.$join.' JOIN '.$c->table.' ON '.$key->funllName.' = '.$forign->fullName;

            $this->on .= $this->forign[$class];

        }

        return $c;

    }


}