<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\Car\Model\UserModel;


class StaffController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    public function encrypt_password($password,$salt){
        return sha1($this->salt.md5($password).$salt);
    }

    /*  用户 */
    function user(){

        View::addData(['getList'=>'admin_user']);
        View::hamlReader('home/list','Admin');
    }


    # 管理用户
    function admin_user(UserModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(68);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_user_get',
                'upd'   => '../staff/admin_user_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_user_del',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
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
                '启用',


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
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],


            ];
            

        # 列表内容
        $where = [];
        
        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
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

        $this->L->adminPermissionCheck(68);
        $model->find($id)->type > 0 && AJAX::error('无权限！');
        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../staff/admin_user_get',
                'upd'   => '../staff/admin_user_upd',
                'back'  => 'staff/user',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '../staff/admin_user_del',

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
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'avatar',
                    'type'  =>  'avatar',
                ],
                
                [
                    'title' =>  '修改密码',
                    'name'  =>  'pwd',
                    'size'  =>  '4',
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
    function admin_user_upd(UserModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(68);
        $model->find($id)->type > 0 && AJAX::error('无权限！');
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['type']);
        unset($data['salt']);
        unset($data['id']);

        $model->where('phone = %n AND id != %d',$data['phone'],$id)->find() && AJAX::error('手机号已存在，请更改为其他手机号！');

        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
            $data['create_time'] = TIME_NOW;
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_user_del(UserModel $model,$id){
        $this->L->adminPermissionCheck(68);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

}