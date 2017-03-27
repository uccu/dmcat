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
use App\Resource\Model\ThemeModel;

class Api extends Controller{


    function __construct(){

        

    }

    function add(ThemeModel $themeModel,Request $request,Resource $resourceModel,Site $siteModel,Token $login,SiteResource $siteResourceModel,$token = '',$name = '',$hash = '',$additional = '',$outlink = ''){

        
        /** 验证登录 **/
        if(!$token && !$login->id)AJAX::error('未登录');
        
        $user_id = $site_id = 0;
        if($token && !$site_id = $siteModel->findToken($token)->id)AJAX::error('未知的站点！');
        else $user_id = $login->id;

        /* 初始化 */
        $info = new stdClass;
        $data = [];

        /* 名字必须存在 */
        $info->name                     = $name;
        !$info->name && AJAX::error('没有名字');

        /* 其他数据 */
        if($hash)$info->hash            = $hash;
        if($additional)$info->additional= $additional;
        $info->user_id                  = $user_id;
        $info->ctime                    = TIME_NOW;

        /* 判断是否有HASH存在 */
        if($hash && $resource = $resourceModel->findHash($hash)){

            $resourceId = $resource->id;

            /* 获取主题 */
            $themeId = $resource->theme_id;
            $theme = $themeId ? $themeModel->find($themeId) : NULL;

            /* 该资源的集数 */
            $number = $resource->new_number;

        }else{

            /* 解析资源 */
            $rns = new RNS($info->name);

            /* 添加额外数据 */
            $info->tags = implode(',',$rns->tag);
            $info->unftags = implode(',',$rns->nameArray);

            /* 获取主题 */
            $theme = $rns->theme;
            $themeId = $theme ? $theme->id : NULL;
            
            /* 添加新资源 */
            $info->new_number = $rns->number; /* 该资源的集数 */
            $themeId && $info->theme_id = $themeId; /* 该资源所属的主题 */
            $resourceId = $resourceModel->set($info)->add()->getStatus();

            !$resourceId && AJAX::error('资源上传失败');

            /* 该资源的集数 */
            $number = $rns->number;


        }


        /* 更新主题 */
        if($theme){

            if($number == $theme->last_number + 1){

                $theme->visible = 1;
                $theme->last_number = $number;
                $theme->number = 1;
                $theme->change_time = TIME_NOW;

            }elseif($number == $theme->last_number)$theme->number += 1;

        }

        /* 外链 */
        if($outlink){

            $link['site_id']        = $site_id;
            $link['resource_id']    = $resourceId;
            $link['outlink']        = $outlink;
            $siteResourceId = $siteResourceModel->set($link)->add(true)->getStatus();

        }


        AJAX::success();

    }

    function sort($name){

        $rns = new RNS($name);

        echo json_encode($rns);

    }

    function delete(Request $request,Resource $resource){
        $id = $request->request('id');
        

        if(!$id)AJAX::error('ID错误');

        if(stripos($id,'-')){

            $ex = explode('-',$id);
            if(is_numeric($ex[0]) && is_numeric($ex[1]) && $ex[1]>$ex[0]){
                
                while($ex[0]<=$ex[1]){

                    $ids[] = $ex[0];
                    $ex[0]++;
                }

            }

        }else{

            $ids = explode(',',$id);
        }

        $data['count'] = $resource->where('%N IN (%c)','id',$ids)->remove()->getStatus();
        $data['ids'] = $ids;

        AJAX::success($data);
    }

    function flesh($id = 0,Resource $resource){


        if(!$id)AJAX::error('ID错误');

        if(stripos($id,'-')){

            $ex = explode('-',$id);
            if(is_numeric($ex[0]) && is_numeric($ex[1]) && $ex[1]>$ex[0]){
                
                while($ex[0]<=$ex[1]){

                    $ids[] = $ex[0];
                    $ex[0]++;
                }

            }

        }else{

            $ids = explode(',',$id);
        }
        
        
        
        foreach($ids as $id){

            $r = $resource->find($id);

            if(!$r)continue;

            $rns = new RNS($r->name);

            $info = new stdClass;
            $info->tags = implode(',',$rns->tag);
            $info->unftags = implode(',',$rns->nameArray);
            //echo $rns->number;die();
    
            foreach($rns->theme as $themeid=>$theme){

                if($rns->number == $theme->last_number+1){
                    $theme->number = 1;
                    $theme->last_number += 1;
                    $theme->change_time = TIME_NOW;
                    $theme->visible = 1;
                    $theme->save();
                    $resource->set(['new_number'=>0])->where('%F=%d','theme_id',$themeid)->save();
                    $info->new_number = 1;

                }elseif($rns->number == $theme->last_number){
                    $info->new_number = 1;
                    if($theme->id != $r->theme_id){
                        $theme->number = 1+$resource->select('count(*) as count','RAW')->where('%F=%d AND new_number=1','theme_id',$themeid)->find()->count;
                        $theme->visible = 1;
                        $theme->save();
                    }else{
                        $theme->number = $resource->select('count(*) as count','RAW')->where('%F=%d AND new_number=1','theme_id',$themeid)->find()->count;
                        $theme->visible = 1;
                        $theme->save();
                    }
                }

                $info->theme_id = $theme->id;break;
            }


            $data['affect'][$id] = $resource->set($info)->save($id)->getStatus();
        }
        AJAX::success($data);

    }



}