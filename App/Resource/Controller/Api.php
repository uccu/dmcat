<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;
use stdClass;
use App\Resource\Middleware\Token;
use App\Resource\Model\ResourceModel as Resource;
use App\Resource\Model\UserModel as User;
use App\Resource\Tool\Format;
use App\Resource\Model\ResourceNameSharp as RNS;

class Api extends Controller{


    function __construct(){

        

    }

    function add(Request $request,Resource $resource,Format $format,User $userModel,Token $login){

        $info = new stdClass;

        $token = $request->request('token');
        if(!$token && !$login->id)AJAX::error('未登录');
        
        if($token)$user_id = $userModel->where(['token'=>$token])->find()->id;
        else $user_id = $login->id;

        $info->name     = $request->request('name');
        $info->outlink  = $request->request('outlink');
        $info->hash     = $request->request('hash');
        $info->ctime    = TIME_NOW;
        $info->user_id  = $user_id;

        $rns = new RNS($info->name);

        foreach($rns->theme as $themeid=>$theme){

            

            if($rns == $theme->last_number+1){

                $theme->last_number += 1;
                $theme->change_time = TIME_NOW;
                $theme->save();
            }
            $info->theme_id = $theme->id;break;
        }

        //获取插入的资源ID
        $id = $resource->set($info)->add()->getStatus();

        if(!$id)AJAX::error('资源上传失败');

        AJAX::success();

    }

    function delete(){
        


    }

    function update(){



    }



}