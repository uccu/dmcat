<?php

namespace App\Admin\Controller;

use Controller;
use View;
use Uccu\DmcatHttp\Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;
use fengqi\Hanzi\Hanzi;
use Model;

use App\Car\Model\UserModel;
use App\Car\Model\DriverModel;
use App\Car\Model\MessageModel;
use App\Car\Model\MessageH5Model;
use App\Car\Model\DriverMessageModel;

class ChatController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        

    }

    
    function send(){

        View::addData(['getList'=>'/admin/chat/admin_send']);
        View::hamlReader('chat/send','Admin');
    }
    function send_driver(){

        View::addData(['getList'=>'/admin/chat/admin_send_driver']);
        View::hamlReader('chat/send_driver','Admin');
    }


    # 发送消息
    function admin_send(UserModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(141);
        
        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'req'   =>[
                    

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
                [
                    'type'=>'checkboxs',
                    'name'=>'choose'
                ],
                '',
                '用户ID',
                '手机号',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [
                [
                    'type'=>'checkboxs',
                    'name'=>'choose'
                ],
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
        $where = $this->setWhere($search);

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

    function admin_send_driver(DriverModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(142);
        
        $name = '司机';
        # 允许操作接口
        $opt = 
            [
                'req'   =>[

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
                [
                    'type'=>'checkboxs',
                    'name'=>'choose'
                ],
                '',
                '用户ID',
                '手机号',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [
                [
                    'type'=>'checkboxs',
                    'name'=>'choose'
                ],
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
        $where = $this->setWhere($search);

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
    
    private function setWhere($search){

        $where = [];

        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        return $where;
    }


    # 发送推送
    function sendPush(MessageModel $model,MessageH5Model $messageH5,$all,UserModel $userModel,$page = 1,$limit = 10,$search,$h5,$message,$toAll,$user_ids){
        $this->L->adminPermissionCheck(141);
        

        if($all && $search){
            $where = $this->setWhere($search);
        }

        if(!$all){
            $userArr = explode(',',$user_ids);
            (!$userArr || !$user_ids) && AJAX::error('请选择用户');
            $where['m'] = ['id IN (%c)',$userArr];
        }
        
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
    function sendPush_driver(DriverMessageModel $model,MessageH5Model $messageH5,DriverModel $userModel,$all,$user_ids,$page = 1,$limit = 10,$search,$h5,$message,$toAll){
        $this->L->adminPermissionCheck(142);

        if($all && $search){
            $where = $this->setWhere($search);
        }

        if(!$all){
            $userArr = explode(',',$user_ids);
            (!$userArr || !$user_ids) && AJAX::error('请选择用户');
            $where['m'] = ['id IN (%c)',$userArr];
        }
        
        $list = $userModel->where($where)->get_field('id')->toArray();

        $out['list'] = $list;

        if($h5)$h5Id = $messageH5->set(['content'=>$h5])->add()->getStatus();
        
        foreach($list as $l){

            $data['driver_id'] = $l;
            $data['content'] = $message;
            $data['create_time'] = TIME_NOW;
            $data['h5'] = $h5Id?$h5Id:'0';
            $model->set($data)->add();
            Func::push_driver($l,$message);
            
        }

        AJAX::success($out);
    }

}