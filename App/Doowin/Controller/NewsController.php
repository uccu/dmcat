<?php

namespace App\Doowin\Controller;


use App\Doowin\Model\NewsGroupModel;
use App\Doowin\Model\NewsHotModel;
use App\Doowin\Model\NewsMediaModel;
use App\Doowin\Model\NewsVideoModel;
use App\Doowin\Model\NewsVideoTypeModel;

use Controller;
use Request;
use Response;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

class NewsController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

    }


    # 集团要闻
        function group_lists(NewsGroupModel $model,$page = 1,$limit = 30,$year = 0,$search = ''){

            $out = [
                'get'=>'/news/group_get',
                'upd'=>'news/group_detail',
                'del'=>'/news/group_del'
            ];

            $out['thead'] = [
                'ID'=>['class'=>'tc'],
                '标题'=>['class'=>'tl'],
                '提交日期'=>['class'=>'tc'],
                '优先级'=>['class'=>'tc'],
                '显示'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'id'    =>['class'=>'tc'],
                'title' =>['class'=>'tl'],
                'create_date'=>['class'=>'tc'],
                'top'   =>['class'=>'tc'],
                'status'=>['type'=>'checkbox','class'=>'tc statusC'],
                '_opt'  =>['class'=>'tc','updateLink'=>1],
            ];
            
            if($year)$where['year'] = $year;
            if($search)$where['search'] = ['title LIKE %n OR description LIKE %n','%'.$search.'%','%'.$search.'%'];

            $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();

            foreach($list as &$v){

                $v->create_date = date('Y-m-d',$v->create_time);
            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function group_get(NewsGroupModel $model,$id){

            !$id && AJAX::success(['info'=>['date'=>date('Y-m-d')]]);
            $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            $info->date = date('Y-m-d',$info->create_time);
            $out['info'] = $info;
            AJAX::success($out);

        }
        function group_upd($id,NewsGroupModel $model,$date = ''){

            $data = Request::getInstance()->request(['status','title','description','title_en','description_en','content_en','top','content','year','pic']);
            if($date)$data['create_time'] = strtotime( $date );
            if($date)$data['year'] = date('Y',$data['create_time']);
            unset ($data['id']);
            if(isset($data['top']))$data['top'] = floor($data['top']);
            
            if(!$id){
                if(!$date)$data['create_time'] = TIME_NOW;
                $id = $model->set($data)->add()->getStatus();
            }
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function group_del($id,NewsGroupModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }
    
    # 热点专题
        function hot_lists(NewsHotModel $model,$page = 1,$limit = 30,$year = 0,$search = ''){

            $out = [
                'get'=>'/news/hot_get',
                'upd'=>'news/hot_detail',
                'del'=>'/news/hot_del'
            ];

            $out['thead'] = [
                'ID'=>['class'=>'tc'],
                '标题'=>['class'=>'tl'],
                '提交日期'=>['class'=>'tc'],
                '优先级'=>['class'=>'tc'],
                '显示'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'id'    =>['class'=>'tc'],
                'title' =>['class'=>'tl'],
                'create_date'=>['class'=>'tc'],
                'top'   =>['class'=>'tc'],
                'status'=>['type'=>'checkbox','class'=>'tc statusC'],
                '_opt'  =>['class'=>'tc','updateLink'=>1],
            ];
            
            if($year)$where['year'] = $year;
            if($search)$where['search'] = ['title LIKE %n OR description LIKE %n','%'.$search.'%','%'.$search.'%'];

            $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();

            foreach($list as &$v){

                $v->create_date = date('Y-m-d',$v->create_time);
            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function hot_get(NewsHotModel $model,$id){

            !$id && AJAX::success(['info'=>['date'=>date('Y-m-d')]]);
            $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            $info->date = date('Y-m-d',$info->create_time);
            $out['info'] = $info;
            AJAX::success($out);

        }
        function hot_upd($id,NewsHotModel $model,$date){

            $data = Request::getInstance()->request(['status','title_en','description_en','content_en','title','description','top','content','year','pic']);
            unset ($data['id']);
            if($date)$data['create_time'] = strtotime( $date );
            if($date)$data['year'] = date('Y',$data['create_time']);
            if(isset($data['top']))$data['top'] = floor($data['top']);
            if(!$id){
                if(!$date)$data['create_time'] = TIME_NOW;
                $id = $model->set($data)->add()->getStatus();
            }
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function hot_del($id,NewsHotModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }
    
    
    # 媒体聚焦
        function media_lists(NewsMediaModel $model,$page = 1,$limit = 30,$year = 0,$search = ''){

            $out = [
                'get'=>'/news/media_get',
                'upd'=>'news/media_detail',
                'del'=>'/news/media_del'
            ];

            $out['thead'] = [
                'ID'=>['class'=>'tc'],
                '标题'=>['class'=>'tl'],
                '提交日期'=>['class'=>'tc'],
                '优先级'=>['class'=>'tc'],
                '显示'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'id'    =>['class'=>'tc'],
                'title' =>['class'=>'tl'],
                'create_date'=>['class'=>'tc'],
                'top'   =>['class'=>'tc'],
                'status'=>['type'=>'checkbox','class'=>'tc statusC'],
                '_opt'  =>['class'=>'tc','updateLink'=>1],
            ];
            
            if($year)$where['year'] = $year;
            if($search)$where['search'] = ['title LIKE %n OR description LIKE %n','%'.$search.'%','%'.$search.'%'];

            $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();

            foreach($list as &$v){

                $v->create_date = date('Y-m-d',$v->create_time);
            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function media_get(NewsMediaModel $model,$id){

            !$id && AJAX::success(['info'=>['date'=>date('Y-m-d')]]);
            $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            $info->date = date('Y-m-d',$info->create_time);
            $out['info'] = $info;
            AJAX::success($out);

        }
        function media_upd($id,NewsMediaModel $model,$date=''){

            $data = Request::getInstance()->request(['status','title_en','description_en','content_en','title','description','top','content','year','pic']);
            unset ($data['id']);
            if($date)$data['create_time'] = strtotime( $date );
            if($date)$data['year'] = date('Y',$data['create_time']);
            if(isset($data['top']))$data['top'] = floor($data['top']);
            if(!$id){
                if(!$date)$data['create_time'] = TIME_NOW;
                $id = $model->set($data)->add()->getStatus();
            }
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function media_del($id,NewsMediaModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }

    # 视频中心
        function video_lists(NewsVideoModel $model,$page = 1,$limit = 30,$year = 0,$search = ''){

            $out = [
                'get'=>'/news/video_get',
                'upd'=>'news/video_detail',
                'del'=>'/news/video_del'
            ];

            $out['thead'] = [
                'ID'=>['class'=>'tc'],
                '标题'=>['class'=>'tl'],
                '提交日期'=>['class'=>'tc'],
                '优先级'=>['class'=>'tc'],
                '显示'=>['class'=>'tc'],
                '推荐'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'id'    =>['class'=>'tc'],
                'title' =>['class'=>'tl'],
                'create_date'=>['class'=>'tc'],
                'top'   =>['class'=>'tc'],
                'status'=>['type'=>'checkbox','class'=>'tc statusC'],
                'banner'=>['type'=>'checkbox','class'=>'tc bannerC'],
                '_opt'  =>['class'=>'tc','updateLink'=>1],
            ];
            
            if($year)$where['year'] = $year;
            if($search)$where['search'] = ['title LIKE %n OR description LIKE %n','%'.$search.'%','%'.$search.'%'];

            $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();

            foreach($list as &$v){

                $v->create_date = date('Y-m-d',$v->create_time);
            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function video_get(NewsVideoModel $model,$id){

            !$id && AJAX::success(['info'=>['date'=>date('Y-m-d')]]);
            $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            $info->date = date('Y-m-d',$info->create_time);
            $out['info'] = $info;
            AJAX::success($out);

        }
        function video_upd($id,NewsVideoModel $model,$date = ''){

            $data = Request::getInstance()->request(['status','banner','video','type','title_en','description_en','content_en','title','description','top','content','year','pic']);
            unset ($data['id']);
            if($date)$data['create_time'] = strtotime( $date );
            if($date)$data['year'] = date('Y',$data['create_time']);
            if(isset($data['top']))$data['top'] = floor($data['top']);
            if(!$id){
                if(!$date)$data['create_time'] = TIME_NOW;
                $id = $model->set($data)->add()->getStatus();
            }
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function video_del($id,NewsVideoModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }


    # 视频分类
        function video_type_lists(NewsVideoTypeModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/news/video_type_get',
                'upd'=>'/news/video_type_upd',
                'del'=>'/news/video_type_del'
            ];

            $out['thead'] = [
                '顺序'=>['class'=>'tc'],
                '名字'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'ord'=>['class'=>'tc'],
                'name'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];

            $list = $model->where($where)->page($page,$limit)->order('ord','id')->get()->toArray();

            foreach($list as &$v){

            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function video_type_get(NewsVideoTypeModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $out['info'] = $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            AJAX::success($out);

        }
        function video_type_upd($id,NewsVideoTypeModel $model){

            $data = Request::getInstance()->request(['name_en','name','ord']);
            unset ($data['id']);
            $data['ord'] = floor($data['ord']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function video_type_del($id,NewsVideoTypeModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }

    # 搜索
    function search($search,$type){
        
        Response::getInstance()->cookie('search',$search,0);
        switch($type){
            case 1:
                AJAX::success(null,200,'/Home/News/inNewsSearch');
                break;
            case 2:
                AJAX::success(null,200,'/Home/News/specialSearch');
                break;
            case 3:
                AJAX::success(null,200,'/Home/News/mediaSearch');
                break;
            case 4:
                AJAX::success(null,200,'/Home/News/videoSearch');
                break;
            default:
                break;
        }

    }
}
