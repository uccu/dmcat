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
use App\App\Model\RecruitModel;
use Model; 


class RecruitController extends Controller{


    function __construct(){


    }


    /** 招聘列表
     * list
     * @param mixed $page 
     * @param mixed $limit 
     * @param mixed $model 
     * @return mixed 
     */
    function lists($page = 1,$limit = 10,RecruitModel $model){
        
        $where['active'] = 1;
        $list = $model->selectExcept('description')->where($where)->page($page,$limit)->get()->toArray();

        $out['list'] = $list;
        AJAX::success($out);
    }


    /** 招聘详情
     * info
     * @param mixed $id 
     * @param mixed $shopModel 
     * @return mixed 
     */
    function info($id,RecruitModel $model){

        $info = $model->find($id);

        !$info && AJAX::error('招聘信息不存在！');

        $out['info'] = $info;
        AJAX::success($out);

    }

    
}