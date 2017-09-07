<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;

# Model
use App\Lawyer\Model\LawyerModel;
use App\Lawyer\Model\BannerModel;
use App\Lawyer\Model\H5Model;
use App\Lawyer\Model\MessageH5Model;
use App\Lawyer\Model\ShareModel;
use App\Lawyer\Model\UserModel;


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

        $banner = $bannerModel->selectExcept('content')->where(['active'=>1])->order('ord')->get()->toArray();

        foreach($banner as &$b){

            $b->ehref = Func::fullAddr('home/banner_h5?id='.$b->id);

        }


        $lawyer = $lawyerModel->select(
            'star','id','name','description','avatar','type','fee_star','feedback_star','average_reply','online_time'
        )->where(['active'=>1])->order('top desc,rand()','RAW')->limit(999)->get()->toArray();

        foreach($lawyer as &$l){
            $l->average_reply = Func::time_zcalculate($l->average_reply);
        }

        $data['banner'] = $banner;
        $data['lawyer'] = $lawyer;
        $data['introduction'] = Func::fullAddr('home/h5?id=1');
        

        AJAX::success($data);

    }

    /** banner h5页面模板
     * h5
     * @param mixed $id 
     * @return mixed 
     */
    function banner_h5($id,BannerModel $model){
        
        $h5 = $model->find($id);
        !$h5 && Response::r302('/404');
        
        View::addData(['data'=>$h5->content]);

        View::hamlReader('h5','App');


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
    function messageH5($id,MessageH5Model $model){
        
        $h5 = $model->find($id);
        !$h5 && Response::r302('/404');
            
        View::addData(['data'=>$h5->content]);
    
        View::hamlReader('h5','App');

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
    

    /** 分享列表
     * mySchool
     * @return mixed 
     */
    function share(ShareModel $model){

        $list = $model->get()->toArray();
        $out['list'] = $list;

        AJAX::success($out);

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
                '标题',
                '预览图片',
                '是否显示',

            ];


        # 列表体设置
        $tbody = 
            [

                'ord',
                'title',
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
    function admin_banner_get(BannerModel $model,$id){

        $this->L->adminPermissionCheck(12);

        $name = '轮播图';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/home/admin_banner_get',
                'upd'   => '/home/admin_banner_upd',
                'back'  => 'home/banner',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/home/admin_banner_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '标题',
                    'name'  =>  'title',
                ],
                [
                    'title' =>  '顺序',
                    'name'  =>  'ord',
                    'size'  =>  '2',
                    'default'=> '0',
                ],
                [
                    'title' =>  '图片',
                    'name'  =>  'pic',
                    'type'  =>  'pic',
                    'description'=>'*比例16:7，建议尺寸375*164'
                ],
                [
                    'title' =>  '跳转类型',
                    'name'  =>  'type',
                    'type'  =>  'select',
                    'default'=> '0',
                    'option'=>  [
                        '0' =>  '无跳转',
                        '1' =>  '内部H5',
                        '2' =>  '外部链接',
                    ]
                ],
                [
                    'title' =>  '外部链接',
                    'name'  =>  'href',
                ],
                [
                    'title' =>  '内部h5',
                    'name'  =>  'content',
                    'type'  =>  'h5',
                ]

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_banner_upd(BannerModel $model,$id){
        $this->L->adminPermissionCheck(12);

        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);
        
        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        
        AJAX::success($out);
    }
    function admin_banner_del(BannerModel $model,$id){
        $this->L->adminPermissionCheck(12);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function admin_h5_get(H5Model $model,$id){
        $this->L->adminPermissionCheck(74);

        

        # 允许操作接口
        $opt = 
            [
                'get'   => '/home/admin_h5_get',
                'upd'   => '/home/admin_h5_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '',
                    'name'  =>  'content',
                    'type'  =>  'h5',
                ]

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $name = $info->title;

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_h5_upd(H5Model $model,$id){
        $this->L->adminPermissionCheck(74);

        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);
        
        $upd = AdminFunc::upd($model,$id,$data);

        $out['upd'] = $upd;
        
        AJAX::success($out);
    }






    # 分享设置
    function admin_share(ShareModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(89);

        $name = '分享';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/home/admin_share_get',
                'upd'   => '/home/admin_share_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/home/admin_share_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->pic ? Func::fullPicAddr($v->pic) : Func::fullPicAddr('nopic.jpg');
        }

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
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
    function admin_share_get(ShareModel $model,$id){

        $this->L->adminPermissionCheck(89);

        $name = '分享';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/home/admin_share_get',
                'upd'   => '/home/admin_share_upd',
                'back'  => 'home/share',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/home/admin_share_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '图片',
                    'name'  =>  'pic',
                    'type'  =>  'pic',
                ],
                [
                    'title' =>  '简介',
                    'name'  =>  'description',
                    'type'  =>  'textarea',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_share_upd(ShareModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(89);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_share_del(ShareModel $model,$id){
        $this->L->adminPermissionCheck(89);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    # 统计
    function admin_statistics($start_time,$end_time){
        $this->L->adminPermissionCheck(109);

        $start_time && $where['e1'] = ['%F >= %n','create_date',$start_time];
        $end_time && $where['e2'] = ['%F <= %n','create_date',$end_time];
        if($start_time){
            $where['type'] = 0;
            $model = UserModel::copyMutiInstance();
            $list = $model->select('COUNT(*) AS `count`,`create_date`','raw')->where($where)->group('create_date')->get('create_date')->toArray();
            
            krsort($list);
            $list = array_values($list);
        }else{
            $list = [];
        }

        



        $name = '统计';
        # 允许操作接口
        $opt = 
            [
                'req'   =>[
                    [
                        'title'=>'开始日期',
                        'name'=>'start_time',
                        'default'=>'2000-01-01',
                        'size'=>'2'
                    ],
                    [
                        'title'=>'结束日期',
                        'name'=>'end_time',
                        'default'=>date('Y-m-d'),
                        'size'=>'2'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '日期',
                '注册人数',

            ];


        # 列表体设置
        $tbody = 
            [

                
                'create_date',
                
                'count',

            ];

        # 分页内容
        $page   = 1;
        $max    = count($list);
        $limit  = $max;

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

        AJAX::success($out);

    }
}