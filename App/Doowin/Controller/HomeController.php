<?php

namespace App\Doowin\Controller;


use App\Doowin\Model\HomeBannerModel;

use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

class HomeController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

    }

    /* banner */
    function banner_lists(HomeBannerModel $model,$page = 1,$limit = 30){

        $out = [
            'get'=>'/home/banner_get',
            'upd'=>'/home/banner_upd',
            'del'=>'/home/banner_del'
        ];

        $out['thead'] = [
            '顺序'=>['class'=>'tc'],
            '轮播图'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'ord'=>['class'=>'tc'],
            'pic'=>['type'=>'imga','class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $list = $model->where($where)->page($page,$limit)->order('ord','id')->get()->toArray();

        foreach($list as &$v){

            $v->pic = '/pic/'.$v->pic;
        }

        $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;

        $out['list']  = $list;
        AJAX::success($out);

    }
    function banner_get(HomeBannerModel $model,$id){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error('没有数据！');
        AJAX::success($out);

    }
    function banner_upd($id,HomeBannerModel $model){

        $data = Request::getInstance()->request(['href','pic','ord']);
        unset ($data['id']);
        $data['ord'] = floor($data['ord']);
        if(!$id)$id = $model->set($data)->add()->getStatus();
        else $model->set($data)->save($id);

        AJAX::success();

    }
    function banner_del($id,HomeBannerModel $model){

        !$id && AJAX::error('删除失败！');
        $model->remove($id);
        AJAX::success();

    }
    


}
