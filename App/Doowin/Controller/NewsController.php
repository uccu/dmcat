<?php

namespace App\Doowin\Controller;


use App\Doowin\Model\NewsGroupModel;

use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

class NewsController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

    }


    /* group */
    function group_lists(NewsGroupModel $model,$page = 1,$limit = 30,$year = 0){

        $out = [
            'get'=>'/news/group_get',
            'upd'=>'news/group_detail',
            'del'=>'/news/group_del'
        ];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            '标题'=>['class'=>'tc'],
            '优先级'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'    =>['class'=>'tc'],
            'title' =>['class'=>'tc'],
            'top'   =>['class'=>'tc'],
            '_opt'  =>['class'=>'tc','updateLink'=>1],
        ];

        if($year)$where['year'] = $year;

        $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();

        foreach($list as &$v){


        }

        $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;

        $out['list']  = $list;
        AJAX::success($out);

    }
    function group_get(NewsGroupModel $model,$id){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error('没有数据！');
        AJAX::success($out);

    }
    function group_upd($id,NewsGroupModel $model){

        $data = Request::getInstance()->request(['title','description','top','content','year','pic']);
        unset ($data['id']);
        $data['top'] = floor($data['top']);
        if(!$id)$id = $model->set($data)->add()->getStatus();
        else $model->set($data)->save($id);

        AJAX::success();

    }
    function group_del($id,NewsGroupModel $model){

        !$id && AJAX::error('删除失败！');
        $model->remove($id);
        AJAX::success();

    }

    


}
