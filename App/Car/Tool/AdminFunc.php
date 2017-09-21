<?php
namespace App\Car\Tool;

use Model;
use stdClass;
use Uccu\DmcatTool\Tool\AJAX;


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


    static function upd(Model $model,$id,$data,$add = true){

        if(!$data)return 0;

        if($add && !$id){

            return $model->set($data)->add()->getStatus();

        }

        
        return $model->set($data)->save($id)->getStatus();

    }

    static function del(Model $model,$id){

        return $model->remove($id)->getStatus();

    }


}