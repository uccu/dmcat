<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;
use stdClass;
use App\Resource\Middleware\Token;
use App\Resource\Model\ResourceModel as Resource;
use App\Resource\Model\UserModel as User;
use App\Resource\Model\SiteModel as Site;
use App\Resource\Model\SiteResourceModel as SiteResource;
use App\Resource\Model\ResourceNameSharp as RNS;

class Api extends Controller{


    function __construct(){

        

    }

    function add(Request $request,Resource $resource,Site $siteModel,Token $login,SiteResource $siteResourceModel){

        $info = new stdClass;
        $data = [];
        $token = $request->request('token');
        if(!$token && !$login->id)AJAX::error('未登录');
        
        if($token){
            $user_id = 0;
            $site_id = $siteModel->where(['token'=>$token])->find()->id;

        }else{
            $user_id = $login->id;
            $site_id = 0;
        }
        $info->name         = $request->request('name');
        
        $info->hash         = $request->request('hash');
        $additional         = $request->request('additional');
        if($additional)$info->additional  = $additional;
        $info->ctime        = TIME_NOW;
        $info->user_id      = $user_id;

        if($resourceId = $resource->where(['hash'=>$info->hash])->find()->id){

            $data['new_resource'] = 0;
        }else{
            $data['new_resource'] = 1;
            $rns = new RNS($info->name);
            $data['theme_id'] = 0;
            $data['new_number'] = 0;

            $info->tags = implode(',',$rns->tag);
            $info->unftags = implode(',',$rns->nameArray);
            //echo $rns->number;
            foreach($rns->theme as $themeid=>$theme){

                if($rns->number == $theme->last_number+1){

                    $theme->last_number += 1;
                    $theme->change_time = TIME_NOW;
                    $theme->save();

                    $resource->set(['new_number'=>0])->where('%F=%d','theme_id',$themeid)->save();
                    $info->new_number = 1;
                    $data['new_number'] = 1;
                }elseif($rns->number == $theme->last_number){
                    $info->new_number = 1;
                }
                $data['theme_id'] = $theme->id;
                $info->theme_id = $theme->id;break;
            }

            //获取插入的资源ID
            $resourceId = $resource->set($info)->add()->getStatus();

        }


        

        $data['resource_id'] = $resourceId;
        $outlink = $request->request('outlink');

        $siteResourceId = $siteResourceModel->set(['site_id'=>$site_id,'resource_id'=>$resourceId,'outlink'=>$outlink])->add(true)->getStatus();

        if(!$resourceId)AJAX::error('资源上传失败');

        AJAX::success($data);

    }

    function delete(){
        


    }

    function flesh(Request $request,Resource $resource){

        $id = $request->request('id');

        if(!$id)AJAX::error('ID错误');

        $r = $resource->find($id);

        if(!$r)AJAX::error('未找到资源');

        $rns = new RNS($r->name);

        $info = new stdClass;
        $info->tags = implode(',',$rns->tag);
        $info->unftags = implode(',',$rns->nameArray);
        
 
        foreach($rns->theme as $themeid=>$theme){

            if($rns->number == $theme->last_number+1){

                $theme->last_number += 1;
                $theme->change_time = TIME_NOW;
                $theme->save();
                $resource->set(['new_number'=>0])->where('%F=%d','theme_id',$themeid)->save();
                $info->new_number = 1;

            }elseif($rns->number == $theme->last_number){
                $info->new_number = 1;
            }

            $info->theme_id = $theme->id;break;
        }


        $data['affect'] = $resource->set($info)->save($id)->getStatus();

        AJAX::success($data);

    }



}