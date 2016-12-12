<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;
use stdClass;

use App\Resource\Model\ResourceModel as Resource;

use App\Resource\Tool\Format;

class Api extends Controller{


    function __construct(){

        

    }

    function add(Request $request,Resource $resource,Format $format){

        $info = new stdClass;

        $token = $request->request('token');

        $info->name     = $request->request('name');
        $info->outlink  = $request->request('outlink');
        $info->hash     = $request->request('hash');
        $info->ctime    = TIME_NOW; 

        //获取插入的资源ID
        $id = $resource->set($info)->add()->getStatus();

        if(!$id)AJAX::error('资源上传失败');

        $format->init($id,$info->name);



        AJAX::success();

    }

    private function formatResource($id,$name){





    }


    private function ImageResolution(){




    }

    private function MovingPictureExpertsGroup($name){

        $array = [
            'MPEG','MPG','DAT',
            'AVI',
            'MOV',
            'ASF',
            'WMV',
            'NAVI',
            '3GP',
            'REAL VIDEO',
            'MKV',
            'FLV',
            'F4V',
            'RMVB',
            'WebM',
            'HDDVD',
   
            ];




    }


    function delete(){
        


    }

    function update(){



    }



}