<?php

namespace App\Admin\Controller;

use Controller;
use View;
use Request;
use App\App\Middleware\L3;
use App\App\Tool\Func;
use App\App\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\App\Model\ShopModel;


# 预约
class ShopController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    function shop(){

        View::addData(['getList'=>'admin_shop']);
        View::hamlReader('home/list','Admin');
    }

    function admin_shop(ShopModel $model,$page,$limit,$status = -2,$search = ''){
        
        $this->L->adminPermissionCheck(140);

        $name = '商品';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../shop/admin_shop_get',
                'upd'   => '../shop/admin_shop_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../shop/admin_shop_del',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title' =>  '搜索',
                        'name'  =>  'search',
                        'size'  =>  4
                    ]   
                    
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '商品名',
                '缩略图',
                '创建时间',
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                [
                    'name'=>'thumb_path',
                    'type'=>'pic'
                ],
                'date'

            ];
            

        # 列表内容
        $where = [];


        if($search){

            $where['search'] = ['name LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->get()->toArray();
        foreach($list as &$v){
            $v->date = date('Y-m-d H:i:s',$v->create_time);
            $v->thumb_path = Func::fullPicAddr($v->thumb);
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
    function admin_shop_get(ShopModel $model,$id){
        
        $this->L->adminPermissionCheck(140);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../shop/admin_shop_get',
            'upd'   => '../shop/admin_shop_upd',
            'back'  => 'shop/shop',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],

            [
                'title' =>  '商品名',
                'name'  =>  'name',
            ],
            [
                'title' =>  '缩略图',
                'name'  =>  'thumb',
                'type'  =>  'pic'
            ],
            [
                'title' =>  '大图',
                'name'  =>  'pic',
                'type'  =>  'picss',
            ],
            [
                'title' =>  '参数',
                'name'  =>  'param',
                'type'  =>  'h5',
            ],
            [
                'title' =>  '详情',
                'name'  =>  'detail',
                'type'  =>  'h5',
            ],
            [
                'title' =>  '电话',
                'name'  =>  'phone',
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
    function admin_shop_upd(ShopModel $model,$id){
        $this->L->adminPermissionCheck(140);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $data['pic'] = Request::getSingleInstance()->request('pic','raw');
        $data['pic'] = \implode(',',$data['pic']);
        unset($data['id']);

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_shop_del(ShopModel $model,$id){
        $this->L->adminPermissionCheck(140);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




}