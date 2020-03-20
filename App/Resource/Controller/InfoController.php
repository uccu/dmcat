<?php

namespace App\Resource\Controller;

use Controller;

use Uccu\DmcatTool\Tool\AJAX;
use DB;
use Uccu\DmcatHttp\Request;

use App\Resource\Model\ResourceModel as Resource;


class InfoController extends Controller{


    function __construct(){

        

    }

    function look($id = 0,Resource $resource,$desc = 0){

        if(!$id)AJAX::error('参数错误');

        $data['info'] = $resource->order(['id'=>$desc])->find($id);
        
        AJAX::success($data);


    }

    function luck(Resource $resource){

        $data['info'] = $resource->order(DB::raw('rand()'))->find();
        AJAX::success($data);
        
    }

    



}