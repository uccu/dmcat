<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use stdClass;
use DB;
use Request;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;

# Model
use App\Lawyer\Model\ConfigModel;
use App\Lawyer\Model\UserModel;
use App\Lawyer\Model\UserProfitModel;


class MoneyController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }

    # 财务设置
    function admin_setting_get(ConfigModel $model){

        $this->L->adminPermissionCheck(104);

        $name = '财务设置';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/money/admin_setting_get',
                'upd'   => '/money/admin_setting_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',

            ];
        $tbody = 
            [
                
                [
                    'title' =>  '客户给平台大使的收益百分比(%)',
                    'name'  =>  'profit_0',
                    'size'  =>  '2'
                ],
                [
                    'title' =>  '客户给平台大使的上一级的收益百分比(%)',
                    'name'  =>  'profit_1',
                    'size'  =>  '2',
                ],
                

            ];



        $info = new stdClass;

        $info->profit_0 = $this->L->config->profit_0;
        $info->profit_1 = $this->L->config->profit_1;

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_setting_upd(ConfigModel $model,$profit_0,$profit_1){
        $this->L->adminPermissionCheck(104);



        $model->set(['value'=>$profit_0])->where(['name'=>'profit_0'])->save();
        $model->set(['value'=>$profit_1])->where(['name'=>'profit_1'])->save();
    
        AJAX::success($out);
    }



    function admin_user(UserModel $model,$page = 1,$limit = 10,$search,$type,$master_type=-2,$sub = -2){
        
        $this->L->adminPermissionCheck(105);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/money/admin_user_get',
                'upd'   => '/money/admin_user_upd',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search'
                    ],

                    [
                        'title'=>'平台大使',
                        'name'=>'master_type',
                        'type'=>'select',
                        'option'=>[
                            '-2'=>'全部',
                            '0'=>'零级平台大使',
                            '1'=>'一级平台大使',
                            '2'=>'二级平台大使',
                        ],
                        'default'=>'-2'
                    ],
                    [
                        'title'=>'结算',
                        'name'=>'sub',
                        'type'=>'select',
                        'option'=>[
                            '-2'=>'全部',
                            '0'=>'未结算',
                        ],
                        'default'=>'-2'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '手机号',
                '名字',
                '平台大使',
                '已结算收益',
                '未结算收益',
                '收益详情'

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
                'id',
                'phone',
                'name',
                'master',
                'profit_o',
                'profit',
                [
                    'name'=>'detail',
                    'href'=>true,
                ],

            ];
            

        # 列表内容
        $where = [];
        $where['type'] = 0;
        
        
        if($master_type != -2){

            $where['master_type'] = $master_type; 

        }else{
            $where['master_type'] = ['%F IN (%c)','master_type',[0,1,2]];
        }

        if($sub == 1){
            $where['sub'] = ['%F = 0','profit'];
        }elseif($sub == 0){
            $where['sub'] = ['%F > 0','profit'];
        }
        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->detail = '<i class="fa fa-pencil text-navy"></i> 查看';
            $v->detail_href = 'staff/profit_detail?id='.$v->id;

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
    function admin_user_get(UserModel $model,$id){

        $this->L->adminPermissionCheck(105);
        $model->find($id)->type > 0 && AJAX::error('无权限！');
        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/money/admin_user_get',
                'back'  => 'staff/profit',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/money/admin_user_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '手机号',
                    'name'  =>  'phone',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                
                [
                    'title' =>  '平台大使',
                    'name'  =>  'master_type',
                    'type'  =>  'select',
                    'option'=>[
                        '0'=>'零级平台大使',
                        '1'=>'一级平台大使',
                        '2'=>'二级平台大使',
                    ],
                    'disabled'=>true
                ],
                
                
                [
                    'title' =>  '已结算收益',
                    'name'  =>  'profit_o',
                    'disabled'=>true,
                    'size'=>'2',
                ],
                [
                    'title' =>  '未结算收益',
                    'name'  =>  'profit',
                    'disabled'=>true,
                    'size'=>'2',
                ],
                [
                    'title' =>  '结算收益',
                    'button'  =>  '结算',
                    'type'  =>  'ajax',
                    'url'=>'/money/submit',
                    'default'=>'0',
                    'refresh'=>true
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }


    function admin_user_detail(UserProfitModel $model,$page = 1,$limit = 10,$search,$type,$id = 0){
        
        $this->L->adminPermissionCheck(105);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                
                'back'  => 'staff/profit',
                'req'   =>[
                    
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '手机号',
                '名字',
                '贡献金额',
                '贡献时间',

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
                'id',
                'phone',
                'name',
                'money',
                'date',
                

            ];
            

        # 列表内容
        $where = [];
        $where['user_id'] = $id;

        $list = $model->select('profit.name','profit.id','profit.avatar','profit.phone','create_time','money')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->date = date('Y-m-d H:i:s',$v->create_time);

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
    
    function submit(UserModel $model,$id,$input){

        $this->L->adminPermissionCheck(68);
        $user = $model->find($id);

        !$user && AJAX::error('用户不存在！');
        !$input && AJAX::error('内容不能为空');

        DB::start();

        $user->profit -= $input;
        $user->profit_o += $input;
        $user->save();

        DB::commit();

        AJAX::success();

    }
    
}