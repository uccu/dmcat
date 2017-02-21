<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;

use View;

use App\Resource\Model\ResourceModel as Resource;
use App\Resource\Model\ThemeModel as Theme;
use App\Resource\Tool\Func;


class ListsController extends Controller{


    function __construct(){

        

    }

    function all(Resource $resource,$asc = 0,$page = 1){

        $list = $resource->where('visible=1')
                ->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->get();
        $data['list'] = $list->toArray();
        AJAX::success($data);

    }

    function null(Resource $resource,$asc = 0,$page = 1){

        $list = $resource->where('ISNULL(theme_id) AND visible=1')
                ->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->get();
        $data['list'] = $list->toArray();
        AJAX::success($data);

    }

    function theme(Resource $resource,$asc = 0,$page = 1,$id = 0,$new_number = 0){

        $resource->where('theme_id=%d',$id);
        if($new_number)$resource->where('new_number=1');
        //note:group_concat
        $list = $resource->where('visible=1')->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->get();
        $data['list'] = $list->toArray();
        AJAX::success($data);

    }

    function new_number(Resource $resourceModel,$asc = 0,$page = 1,$id = 0,Theme $themeModel){

        $list = $resourceModel->select('sitelink.site.name>sname','sitelink.outlink','*')
            ->where('theme_id=%d AND new_number=1 AND visible=1',$id)->order('level DESC','ctime')->get();

        $theme = $themeModel->find($id);
        $data['list'] = $list->toArray();

        $gdata['g']['title'] = $theme->name;
        View::addData($gdata);
        View::addData($data);

        View::hamlReader('Theme/new_number','Resource',$data);

    }


    function new_number2(Resource $resourceModel,$id = 0,Theme $themeModel){

        $list = $resourceModel->select('sitelink.site.name>sname','sitelink.outlink','*')
            ->where('theme_id=%d AND new_number=1 AND visible=1',$id)->order('level DESC','ctime')->get();

        $theme = $themeModel->find($id);
        $data['list'] = $list->toArray();
        foreach($data['list'] as &$theme){
            
            $theme->date = Func::time_calculate($theme->ctime);

            $theme->week = $week[date('w',$theme->ctime)].'/'.$week2[date('w',$theme->ctime)];


        }

        $gdata['g']['title'] = $theme->name;
        View::addData($gdata);
        View::addData($data);

        View::hamlReader('Theme/new_number2','Resource',$data);

    }

    function subtitle(Resource $resource,$asc = 0,$page = 1,$id = 0){

        $list = $resource->where('subtitle_id=%d',$id)->where('visible=1')
                ->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->get();
        $data['list'] = $list->toArray();
        AJAX::success($data);

    }
    
    function resource_type(Resource $resource,$asc = 0,$page = 1,$id = 0){

        $list = $resource->where('resource_type_id=%d',$id)->where('visible=1')
                ->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->get();
        $data['list'] = $list->toArray();
        AJAX::success($data);


    }

    function down_type(Resource $resource,$asc = 0,$page = 1,$id = 0){

        $list = $resource->where('down_type_id=%d',$id)->where('visible=1')
                ->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->get();
        $data['list'] = $list->toArray();
        AJAX::success($data);


    }

    function site(Resource $resource,$asc = 0,$page = 1,$id = 0){

        $resource->where('sitelink.site_id = %d',$id)->where('visible=1')
                ->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->select('*');
                
        $list = $resource->get();

        $data['list'] = $list->toArray();
        AJAX::success($data);

    }

    function search(Resource $resource,$asc = 0,$page = 1,$search = ''){

        if(!$search)AJAX::success([]);

        $list = $resource->where('name like %n','%'.$search.'%')->where('visible=1')
                ->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->get();
        $data['list'] = $list->toArray();
        AJAX::success($data);

    }

    function date(Resource $resource,$asc = 0,$page = 1,$yesterday = 0){

        if($id)$resource->where('ctime BETWEEN (%d,%d)',TIME_YESTERDAY,TIME_NOW);
        else $resource->where('ctime > %d',TIME_NOW);

        $list = $resource->where('visible=1')
                ->order('level DESC','ctime '.($asc?'ASC':'DESC'))
                ->page($page,50)->get();
        $data['list'] = $list->toArray();
        AJAX::success($data);

        
    }



}