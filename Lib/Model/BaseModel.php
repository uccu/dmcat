<?php

namespace Lib\Model;
use Uccu\DmcatTool\Tool\E;

use Uccu\DmcatTool\Tool\LocalConfig as Config;
use Lib\Model\Container;
use Lib\Model\Using;

use Uccu\DmcatTool\Traits\InstanceTrait;

class BaseModel{

    use InstanceTrait;

    public      $table;     //表名
    public      $rawTable;  //设定的别名
    public      $asRawTable;//表sql内全名
    public      $join;   //所有关联的表的信息
    public      $primary;//主键

    protected   $field;  //允许使用的字段
    protected   $updateSafe = true;//UPDATE 是否允许没有WHERE条件
    protected   $deleteSafe = true;//DELETE 是否允许没有WHERE条件
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
    private     $distinct;

    public      $sql;   //输出的sql语句
    public      $link;   //与上一个表的关联信息
    private     $join_cc_r_g_c = [];

    public function __construct($tableName = null){

        $this->tool = Using::getSingleInstance();

        $tableName && $this->table = $tableName;

        $this->rawTable = $this->table;

        $this->tool->mb->prefix && $this->table = $this->tool->mb->prefix.$this->table;

        $this->table = $this->tool->quote_table($this->table);

        $this->asRawTable = $this->tool->quote_table($this->rawTable);
        
        $this->_COLUMNS();

        if(is_string($this->field))$this->field = array($this->field);

        if(!$this->primary)$this->primary = $this->field[0];

    }

    # 如果没有field,查数据库添加字段名字
    private function _COLUMNS(){

        if(!$this->field){

            $this->field = $this->tool->fetch_all('SHOW FULL COLUMNS FROM '.$this->table);


            foreach($this->field as &$v)$v = $v->Field;
            
        }

        return $this;

    }

    # 执行一次查询或操作清理成原来的样子
    public function clean(){

        $this->join     = null;

        $this->select   = null;
        $this->on       = null;
        $this->where    = null;
        $this->group    = null;
        $this->order    = null;
        $this->offset   = null;
        $this->limit    = null;
        $this->set      = null;
        $this->query    = null;
        $this->distinct = null;
        $this->join_cc_r_g_c = [];
        $this->link     = null;

        return $this;
    }

    # group
    public function group($name){

        $field = new Field($name,$this);
        $this->group = $field->fullName;
        return $this;
    }

    # distinct 可用性不强的感觉
    public function distinct($distinct = true){

        $this->distinct = $distinct ? true : false;
    }

    # 执行一次查询操作
    # $key 以$key为键返回
    public function get($key = null){

        $this->importJoin();

        $sql = 'SELECT ';

        if($this->distinct)$sql .= 'DISTINCT ';

        !$this->select && $this->select($this->field);
        

        $sql .= $this->select.' FROM '.($this->join || $this->asRawTable!=$this->table?$this->table.' AS '. $this->asRawTable:$this->table);

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

    # 删除
    public function remove($id = null){

        $this->importJoin();

        $sql = 'DELETE FROM ';

        if($this->join)E::throwEx('Cant Use INSERT or REPLACE With JOIN');

        $sql .= $this->table;

        if(!is_null($id)){

            $field = new Field($this->primary,$this);

            $this->where =  $field->name.' = '.$this->tool->quote($id);

            $sql .= ' WHERE '.$this->where;

        }elseif(!$this->query){

            $this->where = preg_replace('#`\w+`\.#',' ',$this->where); 
            if($this->where)$sql .= ' WHERE '.$this->where;
            
            if($this->deleteSafe && !$this->where)E::throwEx('WHERE Is Empty');

        }else $sql .= ' '.$this->query;

        $this->sql = $sql;

        return new Container($this);

    }

    # 格式化查询返回内容
    public function get_field($field = null,$key = null , $distinct = null){

        if($distinct)$this->distinct($distinct);

        $contain = $this->select($field,$key)->get($key);

        foreach($contain as $k=>$v){
            
            $contain[$k] = $v->$field;
        }

        return $contain;


    }

    # 获取单条
    public function find($id = null){

        $this->limit = 1;

        $container = func_get_args();

        if(count($container)){

            $field = new Field($this->primary,$this);

            $this->where = '';

            $this->where([$this->primary=>$id]);

        }

        return $this->get()->find(0);

    }

    # 保存
    public function save($id = null){

        $this->importJoin();

        $sql = 'UPDATE';

        $sql .= ' '.($this->join || $this->asRawTable!=$this->table

                ? $this->table.' AS '.$this->asRawTable :$this->table);

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

    # 添加 
    public function add($replace = false){

        $this->importJoin();

        $sql = $replace?'REPLACE INTO ':'INSERT INTO ';

        if($this->join)E::throwEx('Cant Use INSERT or REPLACE With JOIN');

        $sql .= $this->table; 

        if(!$this->query){

            if(!$this->set)E::throwEx('Not Set Any Data');

            $this->set = preg_replace('#`\w+`\.#',' ',$this->set); 
            $sql .= ' SET '.$this->set;

            $this->sql = $sql;

        }else $sql .= ' '.$this->query;
        
        $this->sql = $sql;

        return new Container($this);

    }

    # 筛选
    public function where($sql = null , $data = null){

        if(is_string($sql)){

            $container = func_get_args();

            array_shift($container);

            $this->where .= ($this->where?' AND (':'') .$this->tool->format($sql,$container,$this) . ($this->where?' )':'');

        }elseif(is_array($sql) || is_object($sql) ){

            foreach($sql as $k=>$v){

                if(is_array($v))call_user_func_array(array($this,'where'),$v);

                elseif(is_string($v) || is_float($v) || is_int($v))call_user_func_array(array($this,'where'),array('%F = %n',$k,$v));

                elseif(is_null($v))call_user_func_array(array($this,'where'),array('%F IS NULL',$k));
                
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

        if(strtoupper($container[1]) === 'RAW'){

            if(!$container[0]){

            }elseif(is_string($container[0])){

                $this->select = $container[0];return $this;

            }elseif(is_array($container[0])){

                $container = $container[0];
                $sql = $container[0];
                array_shift($container);
                $this->select = $this->tool->format($sql,$container,$this);return $this;
                
            }

        }

        $fields = array();

        foreach($container as $v){

            if(is_null($v))continue;

            if(is_string($v))$v = array($v);

            foreach($v as $j){

                list($j,$as) = explode('>',$j);

                $field = new Field($j,$this);

                if($as)$fields[] = $field->fullName .' AS '. $this->tool->quote_field( $as );

                else $fields[] = $field->asName;
            }
            
        }

        if($fields)$this->select = implode(',',$fields);

        return $this;

    }
    //!----
    public function selectExcept(){

        $container = func_get_args();

        foreach($this->field as $k=>$f){
            if(array_search($f,$container) !== false){
                unset($this->field[$k]);
            }
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

            if(!$container[1])$container[1] = 'ASC';

            $desc = strtoupper($container[1]);

            if($desc==='RAW'){

                $this->order = $container[0];return $this;

            return $this;
            }elseif($desc === 'DESC' || $desc === 'ASC' || is_numeric($desc) || is_bool($desc)){
                if($desc && $desc !== 'ASC')$container[0] .= ' DESC';
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

        foreach($container as $k=>$field){

            if(is_numeric($k))list($field,$desc) = explode(' ',$field);
            else{
                
                $desc = $field;
                $field = $k;
            }
            

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

            }
            
            return $this->on;
        }

        return '';
    }
    protected function join($class,$forign = null,$key = null,$join = 'INNER'){
 
        $c = clone $class::copyMutiInstance();

        if(!$forign)$forign = $this->rawTable.'_id';

        if(!$key)$key = $this->primary;

        $c->link = array($forign,$key,$join);

        return $c;

    }
    public function __get($arg){

        if($this->join_cc_r_g_c[$arg]){
            return $this->join_cc_r_g_c[$arg];
        }

        if(method_exists($this,$arg)){

            $o = $this->$arg();

            if($o instanceof BaseModel && $o != $this){

                $o->rawTable = $arg;

                $o->asRawTable = $this->tool->quote_table( $arg );

                $this->join[] = $arg;

                return $this->join_cc_r_g_c[$arg] = $o;

            }

        }

        return null;

    }
    public function __call($method,$arg){

        $method  = lcfirst(preg_replace('#^import#','',$method));

        if(!method_exists($this,$method))E::throwEx('Method '.$method.' Not Found');

        $this->join[$method] = $this->$method();

    }





}