<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;
use stdClass;

use App\Resource\Model\ResourceModel as Resource;

class Api extends Controller{


    function __construct(){

        

    }

    function add(Request $request,Resource $resource){

        $info = new stdClass;

        $token = $request->request('token');

        $info->name     = $request->request('name');
        $info->outlink  = $request->request('outlink');
        $info->hash     = $request->request('hash');
        $info->ctime    = TIME_NOW; 

        //获取插入的资源ID
        $id = $resource->set($info)->add()->getStatus();

        if(!$id)AJAX::error('资源上传失败');

        $this->formatResource($id,$info->name);



        AJAX::success();

    }

    private function formatResource($id,$name){





    }

    function delete(){
        


    }

    function update(){



    }



}