<?php

namespace Lib\Model;
use Config;
use E;
use Model;

class Using{

    private $sqls = array();

    function __construct(){


        $this->mb = table('Lib/Database/Mysqli');

        $model_null = Config::get('MODEL_NULL');

        $this->model_null = is_null($model_null) || $model_null ? true :false;

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



    function fetch_all($sql, $keyfield = '') {
        
        if(isset($this->sqls[$sql]))return $this->sqls[$sql];

		$data = array();

		$this->query($sql);

		while ($row = $this->mb->fetch_array()) {

            if(!$this->model_null){
                foreach($row as &$v){
                    if(is_null($v))$v = '';
                }
            }

			if ($keyfield && isset($row[$keyfield]))$data[$row[$keyfield]] = $row;
			else $data[] = $row;

		}

		$this->mb->free_result();

        $this->sqls[$sql] = $data;

		return $data;

	}


    function quote_table($tableName){
        
		if(!is_string($tableName))E::throw('Undefined Table\'s Name');

		$str = $this->quote_field($tableName);
		
		return $str;
		
	}


    function quote_field($field ){
		
		if(!is_string($field))E::throw('Undefined Field\'s Name');

        $fields = explode('.',$field);

        foreach($fields as &$v)$v  = '`' . str_replace('`', '', $v) . '`';

        $field = implode('.',$fields);
		
		return $field;

	}

    function quote($field){
		
		
		return $field;

	}
    




}