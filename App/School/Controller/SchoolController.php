<?php

namespace App\School\Controller;


use App\School\Model\SchoolModel;
use Controller;
use App\School\Tool\AJAX;

class SchoolController extends Controller{


    function __construct(){

        

    }


    /* 学校列表 */
    function lists(SchoolModel $model){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            '学校名字'=>['class'=>'tc'],
            '学校名字(EN)'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        

        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['canChange'=>'/school/change_name','class'=>'tc'],
            'name_en'=>['canChange'=>'/school/change_name_en','class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $list = $model->page($page,$limit)->get()->toArray();

        $out['list']  = $list;
        
        AJAX::success($out);



    }


    


}