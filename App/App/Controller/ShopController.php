<?php

namespace App\App\Controller;

use Controller;
use DB;
use stdClass;
use Response;
use Request;
use App\App\Middleware\L;
use App\App\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;
use View;

# 数据模型
use App\App\Model\ShopModel;
use Model; 


class ShopController extends Controller{


    function __construct(){


    }


    /** 商品列表
     * list
     * @param mixed $page 
     * @param mixed $limit 
     * @param mixed $shopModel 
     * @return mixed 
     */
    function lists($page = 1,$limit = 10,ShopModel $shopModel){
        
        $where['active'] = 1;
        $list = $shopModel->selectExcept('detail','param')->where($where)->page($page,$limit)->get()->toArray();

        $out['list'] = $list;
        AJAX::success($out);
    }

    /** 商品详情
     * info
     * @param mixed $id 
     * @param mixed $shopModel 
     * @return mixed 
     */
    function info($id,ShopModel $shopModel){

        $info = $shopModel->find($id);

        !$info && AJAX::error('商品不存在！');

        $out['info'] = $info;
        AJAX::success($out);

    }

    /** 商品详情H5
     * detail
     * @param mixed $id 
     * @param mixed $shopModel 
     * @return mixed 
     */
    function detail($id,ShopModel $shopModel){

        $info = $shopModel->find($id);

        !$info && AJAX::error('商品不存在！');

        View::addData(['title'=>$info->name,'content'=>$info->detail]);
        View::hamlReader('static','App');


    }
}