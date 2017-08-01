<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;

# Model
use App\Lawyer\Model\LawyerModel;
use App\Lawyer\Model\BannerModel;
use App\Lawyer\Model\H5Model;


class HomeController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }

    /** 获取首页的banner以及推荐律师
     * index
     * @param mixed $lawyerModel 
     * @param mixed $banner 
     * @return mixed 
     */
    function index(LawyerModel $lawyerModel,BannerModel $bannerModel){

        $banner = $bannerModel->where(['active'=>1])->order('ord')->get();

        $lawyer = $lawyerModel->select(
            'id','name','description','avatar','type','feedback_star','feedback_star','average_reply'
        )->where(['active'=>1])->order('top desc,rand()','RAW')->limit(10)->get();

        foreach($lawyer as &$l){
            $l->average_reply = Func::time_zcalculate($l->average_reply);
        }

        $data['banner'] = $banner;
        $data['lawyer'] = $lawyer;
        $data['introduction'] = 'home/h5?id=1';
        

        AJAX::success($data);

    }

    /** h5页面模板
     * h5
     * @param mixed $id 
     * @param mixed $model 
     * @return mixed 
     */
    function h5($id,H5Model $model){
        
        $h5 = $model->find($id);
        !$h5 && Response::r302('/404');
        
        View::addData(['data'=>$h5->content]);

        View::hamlReader('h5','App');


    }
    
    /** 获取banner设置参数以及列表
     * admin_banner
     * @param mixed $bannerModel 
     * @return mixed 
     */
    function admin_banner(BannerModel $bannerModel,$page = 1,$limit = 10){
        
        $this->L->adminPermissionCheck(12);

        $name = '轮播图';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/home/admin_banner_get',
                'upd'   => '/home/admin_banner_upd',
                'view'  => '/admin/home/upd',
                'add'   => '/admin/home/upd',
                'del'   => '/home/admin_banner_del',

            ];

        # 头部标题设置
        $thead = 
            [

                '显示顺序',
                '预览图片',
                '是否显示',

            ];


        # 列表体设置
        $tbody = 
            [

                'ord',
                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>true
                ],
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],

            ];
            

        # 列表内容
        $list = $bannerModel->order('ord')->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->pic ? Func::fullPicAddr($v->pic) : Func::fullPicAddr('nopic.jpg');
        }

        # 分页内容
        $page   = $page;
        $max    = $bannerModel->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }

}