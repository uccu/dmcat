<?php

namespace Lib\Model;
use E;

use Config;
use Lib\Model\Container;
use Lib\Model\Using;

class BaseModel{

    const BLOCK = ' ';

    public      $table;     //表名

    public      $rawTable;  //设定的别名

    public      $asRawTable;//表sql内全名

    public      $join;   //所有关联的表的信息

    public      $primary;//主键

    protected   $field;  //允许使用的字段

    protected   $updateSafe = true;//UPDATE 是否允许没有WHERE条件




    private     $cmd;   //sql类型

    private     $select;//筛选

    private     $on;    //join规则

    private     $where; //条件

    private     $group; //按组分

    private     $order; //排序规则

    private     $offset;//开始位置

    private     $limit; //限制数量

    private     $set;   //SET内容

    private     $tool;  //Using工具

    private     $query; //使用HQL不包括select和from





    public      $sql;   //输出的sql语句

    public      $link;   //与上一个表的关联信息

    public function __construct($tableName = null){

        $mb = conf('Mysqli');

        $this->tool = Using::getInstance();

        if($tableName)$this->table = $tableName;

        $this->rawTable = $this->table;

        if($mb->PREFIX)$this->table = $mb->PREFIX.$this->table;

        $this->table = $this->tool->quote_table($this->table);

        $this->asRawTable = $this->tool->quote_table($this->rawTable);
        
        $this->_COLUMNS();

        if(is_string($this->field))$this->field = array($this->field);

        if(!$this->primary)$this->primary = $this->field[0];

    }
    private function _COLUMNS(){

        if(!$this->field){

            $this->field = $this->tool->fetch_all('SHOW FULL COLUMNS FROM '.$this->table);


            foreach($this->field as &$v)$v = $v->Field;

            
        }

        return $this;

    }
    public function group($n){

        $field = new Field($n,$this);

        $this->group = $field->fullName;

        return $this;

    }
    public function get($key = null){

        $this->importJoin();

        $this->cmd = 'SELECT ';

        $sql = $this->cmd;

        if(!$this->select)$this->select = '*';

        $sql .= $this->select.' FROM '.($this->join && $this->asRawTable!=$this->table?$this->asRawTable.' AS '.$this->table :$this->table);

        if($this->on)$sql .= $this->on;

        if(!$this->query){

            if($this->where)$sql .= ' WHERE '.$this->where;

            if($this->group)$sql .= ' GROUP BY '.$this->group;

            if($this->order)$sql .= ' ORDER BY '.$this->order;

            if($this->limit)$sql .= ' LIMIT '.$this->limit;

            if($this->offset)$sql .= ' OFFSET '.$this->offset;

        }else $sql .= ' '.$this->query;

        $this->sql = $sql;

        return new Container($this,$key);

    }
    public function find($id = 0){

        $this->limit = 1;

        if($id){

            $field = new Field($this->primary,$this);

            $this->where =  $field->fullName.' = '.$this->tool->quote($id);

        }

        return $this->get()->find(0);

    }
    public function save($id = null){

        $this->importJoin();

        $this->cmd = 'UPDATE';

        $sql = $this->cmd;

        $sql .= ' '.($this->join && $this->asRawTable!=$this->table

                ? $this->asRawTable.' AS '.$this->table :$this->table);

        if($this->on)$sql .= $this->on;

        if(!$this->query){

            if(!$this->set)E::throwEx('Not Set Any Data');

            $sql .= ' SET '.$this->set;

            if(!is_null($id)){

                $field = new Field($this->primary,$this);

                $this->where =  $field->fullName.' = '.$this->tool->quote($id);

            }
            if($this->updateSafe && !$this->where)E::throwEx('WHERE Is Empty');

            elseif($this->where)$sql .= ' WHERE '.$this->where;

            $this->sql = $sql;

        }else $sql .= ' '.$this->query;
        
        $this->sql = $sql;

        return new Container($this);

    }
    public function add($replace = false){

        $this->importJoin();

        $this->cmd = $replace?'REPLACE INTO ':'INSERT INTO ';

        $sql = $this->cmd;

        if($this->join)E::throwEx('Cant Use INSERT or REPLACE With JOIN');

        $sql .= $this->table;

        if(!$this->query){

            if(!$this->set)E::throwEx('Not Set Any Data');

            $sql .= ' SET '.$this->set;

            $this->sql = $sql;

        }else $sql .= ' '.$this->query;
        
        $this->sql = $sql;

        return new Container($this);

    }
    public function where($sql = null , $data = null){

        if(is_string($sql)){

            $container = func_get_args();

            array_shift($container);

            $this->where .= ($this->where?' AND (':'') .$this->tool->format($sql,$container,$this) . ($this->where?' )':'');

        }elseif(is_array($sql)){

            foreach($sql as $k=>$v){

                if(is_array($v))call_user_func_array(array($this,'where'),$v);

                elseif(is_string($v))call_user_func_array(array($this,'where'),array('%F = %n',$k,$v));
                
            }

        }

        return $this;

    }
    public function set($sql = null ){

        if(is_string($sql)){

            $container = func_get_args();

            array_shift($container);

            $this->set .= ($this->set?' ,':'') .$this->tool->format($sql,$container,$this) ;

        }elseif(is_array($sql) || is_object($sql) ){

            foreach($sql as $k=>$v){

                if(is_array($v))call_user_func_array(array($this,'set'),$v);

                elseif(!is_object($v))call_user_func_array(array($this,'set'),array('%F = %n',$k,$v));

            }

        }

        return $this;

    }
    public function query($sql=null,$s = array()){

        if($sql)$this->query = $this->tool->format($sql,$s,$this);

        return $this;

    }
    public function select(){

        $container = func_get_args();

        $fields = array();

        foreach($container as $v){

            if(is_string($v))$v = array($v);

            foreach($v as $j){

                $field = new Field($j,$this);

                $fields[] = $field->asName;
            }
            
        }

        if($fields)$this->select = implode(',',$fields);

        return $this;

    }
    //!----
    public function selectExcept(){

        $container = func_get_args();

        foreach($container as $field){

            unset($this->$field[$field]);
        }

        return $this;

    }
    public function offset($i = null){

        if(!$i)return $this;

        $i = floor($i);

        if($i<0)$i = 0;

        $this->offset = $i;

        return $this;

    }
    public function limit($i = null){

        if(!$i)return $this;

        $i = floor($i);

        if($i<1)$i = 1;

        $this->limit = $i;

        return $this;

    }
    public function page($page = 1,$count = null){
        
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
    public function order(){

        $container = func_get_args();

        $count = count($container);

        if(!$count)return $this;

        if($count === 2){

            if(!$container[1])$container[1] = '';

            $desc = strtoupper($container[1]);

            if(!$desc || $desc === 'ASC'){

                $count = 1;

            }

        }

        if($count === 1){

            $field = reset($container);

            if(!$field)return $this;

            list($field,$desc) = explode(' ',$field);

            $field = new Field($field ,$this);

            $this->order = $field->fullName.' '. (!$desc || strtoupper($container[1]) ==='ASC' ?'ASC':'DESC');

            return $this;
        }

        $orders = array();

        foreach($container as $field){

            list($field,$desc) = explode(' ',$field);

            $field = new Field($field ,$this);

            $orders[] = $field->fullName.' '. (!$desc || strtoupper($container[1]) ==='ASC' ?'ASC':'DESC');

        }

        $this->order = implode(', ' ,$orders);

        return $this;

    }
    public function __toString(){

        return (string)$this->sql;

    }
    public function hasField($field){

        return $field=='*' || in_array($field,$this->field) ? true : false;
       
    }
    public function getKeyField($field){

        return array_search($field,$this->field);
       
    }
    public function importJoin(){
        
        if($this->join){

            $this->on = '';

            foreach($this->join as $m){

                $m = $this->$m;

                $foreign = new Field($m->link[0],$m);

                $key = new Field($m->link[1],$this);

                $this->on .= ' '.$m->link[2].' JOIN '.$foreign->fullTable.' ON '.$foreign->fullName.' = '.$key->fullName;

                $this->on .= $m->importJoin();

                return $this->on;

            }
        }

        return '';
    }
    protected function join($class,$forign = null,$key = null,$join = 'INNER'){
 
        $c = clone table($class);

        if(!$forign)$forign = $this->rawTable.'_id';

        if(!$key)$key = $this->primary;

        $c->link = array($forign,$key,$join);

        return $c;

    }
    public function __get($arg){

        if(method_exists($this,$arg)){

            $o = $this->$arg();

            if($o instanceof BaseModel && $o != $this){

                $o->rawTable = $arg;

                $o->asRawTable = $this->tool->quote_table( $arg );

                $this->join[] = $arg;

                return $this->$arg = $o;

            }

        }

        return null;

    }
    public function __call($method,$arg){

        $method  = lcfirst(preg_replace('#^import#','',$method));

        if(!method_exists($this,$method))E::throwEx('Method '.$method.' Not Found');

        $this->join[$method] = $this->$method();

    }
    public static function getInstance(){

        if($name = func_get_args()){
            $name = $name[0];
            return clone table(get_called_class(),$name);


        }

        return clone table(get_called_class());

    }




}