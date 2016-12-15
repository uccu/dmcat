<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;

use App\Resource\Model\ResourceModel as Resource;


class InfoController extends Controller{


    function __construct(){

        

    }

    function look($id = 0,Resource $resource,$desc = 0){

        if(!$id)AJAX::error('参数错误');

        $data['info'] = $resource->select('*','theme.id>themeid')->order('id',$desc)->find($id);
        
        AJAX::success($data);


    }

    function luck(Resource $resource){

        $data['info'] = $resource->order('rand()','raw')->find();
        AJAX::success($data);
        
    }

    



}