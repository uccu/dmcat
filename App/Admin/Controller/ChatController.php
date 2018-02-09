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
use App\Car\Model\MessageModel;
use App\Car\Model\MessageH5Model;

use App\Admin\Set\Lists;

class ChatController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        

    }

    
    function send(){

        View::addData(['getList'=>'/admin/chat/admin_send']);
        View::hamlReader('chat/send','Admin');
    }



    # 发送消息
    function admin_send(UserModel $model,$page = 1,$limit = 10,$search){
        

        $m = Lists::getSingleInstance($model,$page,$limit);
        # 权限
        // $m->checkPermission(141);
        # 允许操作接口
        $m->setOptReq(['title'=>'搜索','name'=>'search','size'=>'3']);



        # 设置名字
        $m->setName('用户');
        # 设置表头
        $m->setHead(['type'=>'checkboxs','name'=>'choose']);
        $m->setHead('');
        $m->setHead('用户ID');
        $m->setHead('手机号');
        $m->setHead('名字');

       
        # 设置表体
        $m->setBody(['type'=>'checkboxs','name'=>'choose']);
        $m->setBody(['name'=>'fullPic','type'=>'pic','href'=>false,'size'=>'30']);
        $m->setBody('id');
        $m->setBody('phone');
        $m->setBody('name');

        
        # 筛选
        $m->where = $this->setWhere($search);

        # 获取列表
        $model->order('create_time desc');
        $m->getList(0);

        $m->each(function($v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
        });

        $m->output();

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
        // $this->L->adminPermissionCheck(141);
        

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
    

}