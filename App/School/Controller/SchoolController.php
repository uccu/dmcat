<?php

namespace App\School\Controller;


use App\School\Model\SchoolModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;

class SchoolController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 学校列表 */
    function lists(SchoolModel $model){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/school/get','upd'=>'/school/upd','del'=>'/school/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->school->school_name)=>['class'=>'tc'],
            ($this->lang->school->school_name_en)=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        

        $list = $model->get()->toArray();

        $out['list']  = $list;
        AJAX::success($out);


    }

    function get($id,SchoolModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

    function upd($id,SchoolModel $model){

        $data = Request::getInstance()->request(['name','name_en']);

        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }


    function del($id,SchoolModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }


}