<?php

namespace App\Car\Controller;

use Controller;
use Uccu\DmcatTool\Tool\AJAX;
use Response;
use View;
use Request;
use stdClass;
use App\Car\Tool\Func;
use App\Car\Middleware\L;
use Model;

use App\Car\Model\H5Model;
use App\Car\Model\AreaModel;
use App\Car\Model\ActivityModel;



class HomeController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }


    function upAvatar(){

        $out['path'] = Func::uploadFiles('file',100,100);
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);
    }

    function uploadPic(){

        $out['path'] = Func::uploadFiles('file');
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);

    }
    function uploadFile(){
        
        $id = Func::upload('file');
        if(!$id)AJAX::error('no file');
        $out['path'] = $id;
        $out['fpath'] = '/pic/file.jpg';
        $out['apath'] = Func::fullPicAddr('file.jpg');
        AJAX::success($out);
    }

    /** 获取省市
     * area
     * @param mixed $id 
     * @param mixed $areaModel 
     * @return mixed 
     */
    function area($id,AreaModel $areaModel){

        if(!$id){

            $list = $areaModel->where(['parent_id'=>0])->order('pinyin')->get_field('areaName','id')->toArray();
        }else{

            $list = $areaModel->where(['parent_id'=>$id])->order('pinyin')->get_field('areaName','id')->toArray();
        }
        $out['list'] = $list;
        AJAX::success($out);
    }


    
    /** 获取H5页面
     * h5
     * @param mixed $id 
     * @param mixed $model 
     * @return mixed 
     */
    function h5($id,H5Model $model){

        $m = $model->find($id);

        if($m)View::addData(['title'=>$m->name,'content'=>$m->content]);

        View::hamlReader('h5','App');
    }




    /** 获取活动
     * getActivities
     * @param mixed $model 
     * @return mixed 
     */
    function getActivities(ActivityModel $model){

        $list = $model->where('status=1')->order('level desc','create_time desc')->get()->toArray();
        $out['list'] = $list;
        AJAX::success($out);

    }

    
    
}