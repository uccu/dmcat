<?php
namespace App\Lawyer\Tool;

use Model;
use stdClass;
use AJAX;

class AdminFunc{
    
    static function get(Model $model,$id,$ajax_error = false){

        $info = $model->find($id);

        $ajax_error && !$info && AJAX::error('查询失败！');
        
        if(!$info){

            $info = new stdClass;
            foreach($model->field as $field)$info->$field = '';
            
        }

        return $info;

    }


    static function upd(Model $model,$id,$data){

        return $model->set($data)->save($id)->getStatus();

    }
}