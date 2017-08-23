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
use App\Lawyer\Model\UserModel;
use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\FastQuestionModel;
use App\Lawyer\Model\ConsultModel;
use App\Lawyer\Model\MessageModel;
use App\Lawyer\Model\MessageH5Model;


class ChatController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }

    function admin_fast(FastQuestionModel $model,$page = 1,$limit = 10){
        
        $this->L->adminPermissionCheck(86);

        $name = '管理员';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/chat/admin_fast_get',
                'upd'   => '/chat/admin_fast_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/chat/admin_fast_del',

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '问题',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'content',

            ];
            

        # 列表内容
        $where = [];

        $list = $model->where($where)->page($page,$limit)->get()->toArray();

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
    function admin_fast_get(FastQuestionModel $model,$id){

        $this->L->adminPermissionCheck(86);

        $name = '问题管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/chat/admin_fast_get',
                'upd'   => '/chat/admin_fast_upd',
                'back'  => 'chat/fast',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/chat/admin_fast_del',

            ];
        $tbody = 
            [

                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '问题',
                    'name'  =>  'content',
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
    function admin_fast_upd(FastQuestionModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(86);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);

        
        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_fast_del(FastQuestionModel $model,$id){
        $this->L->adminPermissionCheck(86);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    # 发送消息
    function admin_send(UserModel $model,UserConsultLimitModel $lmodel,$page = 1,$limit = 10,$search,$all,$normal,$vip,$vip0,$vip1,$vip2){
        
        $this->L->adminPermissionCheck(100);
        
        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/user/admin_user_get',
                'req'   =>[
                    
                    [
                        'title'=>'全部用户',
                        'name'=>'all',
                        'type'=>'checkbox',
                        
                    ],
                    [
                        'title'=>'普通用户',
                        'name'=>'normal',
                        'type'=>'checkbox',
                        
                    ],
                    [
                        'title'=>'所有会员',
                        'name'=>'vip',
                        'type'=>'checkbox',
                        
                    ],
                    [
                        'title'=>'法律会员',
                        'name'=>'vip0',
                        'type'=>'checkbox',
                        
                    ],
                    
                    [
                        'title'=>'签证会员',
                        'name'=>'vip2',
                        'type'=>'checkbox',
                        
                    ],
                    [
                        'title'=>'留学转学会员',
                        'name'=>'vip1',
                        'type'=>'checkbox',
                        
                    ],
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'6'
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
                

            ];
            

        # 列表内容
        $where = $this->setWhere($lmodel,$search,$all,$normal,$vip,$vip0,$vip1,$vip2);

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->lawyer = '<i class="fa fa-pencil text-navy"></i> 查看';
            $v->lawyer_href = 'chat/user_chat?id='.$v->id;
            $v->school = '<i class="fa fa-pencil text-navy"></i> 查看';
            $v->school_href = 'school/user_school?id='.$v->id;
            
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
    
    private function setWhere($lmodel,$search,$all,$normal,$vip,$vip0,$vip1,$vip2){

        $where = [];
        $where['type'] = 0;
        
        $vip0 && $vip1 && $vip2 && $vip = 1;
        $normal && $vip && $all = 1;

        if($all){

        }elseif($vip){
            $where['e2'] = ['EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F AND `death_time`>%n)','id',TIME_NOW];
        
        }elseif($normal && !$vip0 && !$vip1 && !$vip2 && !$vip){
            $where['e2'] = ['NOT EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F)','id'];
        
        }elseif($normal && $vip0 && !$vip1 && !$vip2 && !$vip){
            $where['e2'] = ['NOT EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F) OR EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`=1 AND `user_id` = %F AND `death_time`>%n)','id','id',TIME_NOW];
        }elseif($normal && !$vip0 && $vip1 && !$vip2 && !$vip){
            $where['e2'] = ['NOT EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F) OR EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`=2 AND `user_id` = %F AND `death_time`>%n)','id','id',TIME_NOW];
        }elseif($normal && !$vip0 && !$vip1 && $vip2 && !$vip){
            $where['e2'] = ['NOT EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F) OR EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`=3 AND `user_id` = %F AND `death_time`>%n)','id','id',TIME_NOW];
        
        }elseif($normal && $vip0 && $vip1 && !$vip2 && !$vip){
            $where['e2'] = ['NOT EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F) OR EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`!=3 AND `user_id` = %F AND `death_time`>%n)','id','id',TIME_NOW];
        }elseif($normal && $vip0 && !$vip1 && $vip2 && !$vip){
            $where['e2'] = ['NOT EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F) OR EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`!=2 AND `user_id` = %F AND `death_time`>%n)','id','id',TIME_NOW];
        }elseif($normal && !$vip0 && $vip1 && $vip2 && !$vip){
            $where['e2'] = ['NOT EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F) OR EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`!=1 AND `user_id` = %F AND `death_time`>%n)','id','id',TIME_NOW];
        
        }elseif(!$normal && $vip0 && $vip1 && !$vip2 && !$vip){
            $where['e2'] = ['EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`!=3 AND `user_id` = %F AND `death_time`>%n)','id',TIME_NOW];
        }elseif(!$normal && $vip0 && !$vip1 && $vip2 && !$vip){
            $where['e2'] = ['EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`!=2 AND `user_id` = %F AND `death_time`>%n)','id',TIME_NOW];
        }elseif(!$normal && !$vip0 && $vip1 && $vip2 && !$vip){
            $where['e2'] = ['EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`!=1 AND `user_id` = %F AND `death_time`>%n)','id',TIME_NOW];
        
        }elseif(!$normal && $vip0 && !$vip1 && !$vip2 && !$vip){
            $where['e2'] = ['EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`=1 AND `user_id` = %F AND `death_time`>%n)','id',TIME_NOW];
        }elseif(!$normal && !$vip0 && $vip1 && !$vip2 && !$vip){
            $where['e2'] = ['EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`=2 AND `user_id` = %F AND `death_time`>%n)','id',TIME_NOW];
        }elseif(!$normal && $vip0 && !$vip1 && $vip2 && !$vip){
            $where['e2'] = ['EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `rule_id`=3 AND `user_id` = %F AND `death_time`>%n)','id',TIME_NOW];
        
        }




        

        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        return $where;
    }

    # 发送短信
    function sendMail(UserModel $model,UserConsultLimitModel $lmodel,$page = 1,$limit = 10,$search,$all,$normal,$vip,$vip0,$vip1,$vip2){
        $this->L->adminPermissionCheck(100);
        $where = $this->setWhere($lmodel,$search,$all,$normal,$vip,$vip0,$vip1,$vip2);
        
        $list = $model->where($where)->get_field('id')->toArray();

        $out['list'] = $list;

        AJAX::success($out);
    }

    # 发送推送
    function sendPush(MessageModel $model,MessageH5Model $messageH5,UserModel $userModel,UserConsultLimitModel $lmodel,$page = 1,$limit = 10,$search,$all,$normal,$vip,$vip0,$vip1,$vip2,$h5,$message,$toAll){
        $this->L->adminPermissionCheck(100);
        $where = $this->setWhere($lmodel,$search,$all,$normal,$vip,$vip0,$vip1,$vip2);
        
        $list = $userModel->where($where)->get_field('id')->toArray();

        $out['list'] = $list;

        if($h5)$h5Id = $messageH5->set(['content'=>$h5])->add()->getStatus();
        
        foreach($list as $l){

            $data['user_id'] = $l;
            $data['content'] = $message;
            $data['create_time'] = TIME_NOW;
            $data['h5'] = $h5Id?$h5Id:'0';
            $model->set($data)->add();
            Func::push($l,$message);
            
        }

        AJAX::success($out);
    }

    # 绑定的律师列表
    function admin_user_chat(ConsultModel $consultModel,LawyerModel $model,$id){

        $this->L->adminPermissionCheck(68);

        $name = '管理员';
        # 允许操作接口
        $opt = 
            [

                'back'  => 'staff/user'
                
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '律师ID',
                '律师类型',
                '名字',
                '聊天记录'

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
                'lawyer_id',
                'typeName',
                'name',
                [
                    'name'=>'look',
                    'href'=>true,
                ],

            ];
            

        # 列表内容
        $where['user_id'] = $id;
        $consultModel->distinct();

        $lawyer_list = $consultModel->where($where)->get_field('lawyer_id')->toArray();
        
        $list = [];
        foreach($lawyer_list as $v){

            $list[] = $consultModel->select('content','create_time','lawyer_id','lawyer.name','lawyer.avatar','lawyer.type')->where($where)->where(['lawyer_id'=>$v])->order('create_time desc')->find();
        }


        $types = ['法律','留学转学','签证'];

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->typeName = $types[$v->type];
            $v->look = '<i class="fa fa-pencil text-navy"></i> 查看';
            $v->look_href = 'chat/user_chats?lawyer_id='.$v->lawyer_id.'&user_id='.$id;
        }

        # 分页内容
        $page   = $page;
        $max    = count($list);
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

    # 与律师聊天记录
    function admin_user_chats(ConsultModel $consultModel,LawyerModel $model,$user_id,$lawyer_id,$page = 1,$limit  = 10){
        
        $this->L->adminPermissionCheck(68);

        $name = '管理员';
        # 允许操作接口
        $opt = 
            [

                'back'  => 'chat/user_chat?id='.$user_id
                
            ];

        # 头部标题设置
        $thead = 
            [

                '时间',
                '发送者',
                '内容'

            ];


        # 列表体设置
        $tbody = 
            [

                'date',
                'name',
                'content',

            ];
            

        # 列表内容
        $where['user_id'] = $user_id;
        $where['lawyer_id'] = $lawyer_id;

        $list = $consultModel->select('*','lawyer.avatar>lawyer_avatar','lawyer.name>lawyer_name','user.name>user_name','user.avatar>user_avatar')->where($where)->page($page,$limit)->order('create_time desc')->get()->toArray();


        foreach($list as &$v){
            $v->date = date('m-d H:i',$v->create_time);
            $v->name = $v->which ? $v->lawyer_name : $v->user_name;
        }

        # 分页内容
        $page   = $page;
        $max    = $consultModel->select('COUNT(*) as c','RAW')->where($where)->find()->c;
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