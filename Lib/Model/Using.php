<?php

namespace Lib\Model;
use Config;
use E;
use Model;

use Lib\Sharp\SingleInstance;

class Using implements SingleInstance{

    # 缓存sql，重复利用查询
    private $sqls = [];


    function __construct(){

        $model_null     = Config::get('MODEL_NULL');
        $this->database = Config::get('DATABASE');

        $this->model_null = is_null($model_null) || $model_null ? true :false;

    }
    function __get($name){

        if($name=='mb'){

            $mod = '\Lib\Database\\'.$this->database;

            return $this->mb = $mod::getSingleInstance();
        }
        return null;

    }
    function commit(){
		return $this->mb->commit();
	}
	function rollback(){
		return $this->mb->rollback();
	}
    function start(){
		return $this->mb->start();
	}
    function query($sql){

        $ret = $this->mb->query($sql);
        
        if ($ret) {

            $cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));

            if ($cmd === 'UPDATE' || $cmd === 'DELETE') {

                $ret = $this->mb->affected_rows();

            } elseif ($cmd === 'INSERT') {

                $ret = $this->mb->insert_id();

            }

        }

        return $ret;


    }
    function fetch_all($sql, $keyfield = '',$model = null) {

        if(isset($this->sqls[$sql]))return $this->sqls[$sql];

		$data = array();

		$this->query($sql);

		while ($row = $this->mb->fetch_array()) {

            if(!$this->model_null){
                foreach($row as &$v){
                    if(is_null($v))$v = '';
                }
            }

			if ($keyfield && isset($row[$keyfield]))$data[$row[$keyfield]] = new Record($row,$model);
			else $data[] = new Record($row,$model);

		}

		$this->mb->free_result();

        $this->sqls[$sql] = $data;

		return $data;

	}
    function quote_table($tableName){
        
		if(!is_string($tableName))E::throwEx('Undefined Table\'s Name');

		$str = $this->quote_field($tableName);
		
		return $str;
		
	}
    function quote_field($field ){
		
		if(!is_string($field))E::throwEx('Undefined Field\'s Name');

        $fields = explode('.',$field);

        foreach($fields as &$v)$v  = '`' . str_replace('`', '', $v) . '`';

        $field = implode('.',$fields);
		
		return $field;

	}
    function quote($str){
		
        if (is_string($str))return '\'' . addcslashes($str, "\n\r\\'\"\032") . '\'';

		if (is_int($str) or is_float($str))return  $str ;

		if (is_array($str)) {

			foreach ($str as &$v)$v = $this->quote($v, true);
			
			return $str;

		}

		if (is_bool($str))return $str ? '1' : '0';

        if (is_null($str))return 'NULL';

		return '\'\'';

	}
    function format($hql = '', $arg = array() ,Model $model ,$checkField = true) {

        $sql = preg_replace_callback('#([ =\-,\+\(]|^)([a-z\*][a-zA-Z0-9_\.]*)#',function($m) use ($model,$checkField){
            if(substr_count($m[2],'.')==0 && $checkField && !$model->hasField($m[2]))return $m[0];
            $field = new Field($m[2],$model,$checkField);
            return $m[1].$field->fullName;
        },$hql);
		$count = substr_count($sql, '%');

		if (!$count) {
			return $sql;
		} elseif ($count > count($arg)) {
			E::throwEx('Sql Needs '.$count.' Args' );
		}

		$len = strlen($sql);
		$i = $find = 0;
		$ret = '';
		while ($i <= $len && $find < $count) {
			if ($sql{$i} == '%') {
				$next = $sql{$i + 1};

                switch($next){
                    case 'F':
                        $field = new Field($arg[$find],$model);
                        $ret .= $field->fullName;
                        break;
                    case 'N':
                        $field = new Field($arg[$find],$model);
                        $ret .= $field->name;
                        break;
                    case 's':
                        $ret .= $this->quote(serialize($arg[$find]));
                        break;
                     case 'j':
                        $ret .= $this->quote(json_encode($arg[$find]));
                        break;
                    case 'f':
                        $ret .= sprintf('%F', $arg[$find]);
                        break;
                    case 'd':
                        $ret .= floor($arg[$find]);
                        break;
                    case 'i':
                        $ret .= $arg[$find];
                        break;
                    case 'b':
                        $ret .= $this->quote(base64_encode($arg[$find]));
                        break; 
                    case 'c':
                        $ret .= implode(',',$this->quote($arg[$find]));
                        break;
                    case 'a':
                        $ret .= implode(' AND ',$thisl->quote($arg[$find]));
                        break;
                    default:
                        $ret .= $this->quote($arg[$find]);
                        break;

                }

                $i++;
				$find++;
				
			} else {
				$ret .= $sql{$i};
			}
			$i++;
		}
		if ($i < $len) {
			$ret .= substr($sql, $i);
		}
		return $ret;
	}

    public static function getInstance(){
        static $object;
		if(empty($object))$object = new self();
		return $object;
    }



}