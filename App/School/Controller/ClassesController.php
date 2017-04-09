<?php

namespace App\School\Controller;


use App\School\Model\ClassesModel;
use Controller;
use App\School\Tool\AJAX;

class ClassesController extends Controller{


    function __construct(){

        

    }


    /* 班级列表 */
    function lists(ClassesModel $model){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            '班级名字'=>['class'=>'tc'],
            '班级名字(EN)'=>['class'=>'tc'],
            '操作'=>['class'=>'tc'],
        ];
        

        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $list = $model->page($page,$limit)->get()->toArray();

        $out['list']  = $list;
        
        AJAX::success($out);



    }


    


}