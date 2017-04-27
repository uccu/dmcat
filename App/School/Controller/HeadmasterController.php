<?php

namespace App\School\Controller;


use Controller;
use Response;

use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use App\School\Model\SchoolMessageModel;
use View;
use Model;

class HeadmasterController extends Controller{

    private $L;


    function __construct(){

        $this->L = L::getInstance();
    }

    function list(){

        View::hamlReader('Headmaster/'.__FUNCTION__,'App');
    }

    function view_message($type = 0,$istag = 0,$page = 1,$limit = 30,SchoolMessageModel $model){

        $type && $where['type'] = $type;
        $istag && $where['istag'] = $istag;

        $list = $model->select('id','istag','isread','type','user.avatar','user.name','user.name_en','create_time')->where($where)->page($page,$limit)->order('create_time','DESC')->get()->toArray();

        foreach($list as &$v){
            
            $v->message = $v->name.'/'.$v->name_en.($v->type == 1 ? '家长':'老师').'给您发了一条留言';
            $v->fullAvatar = Func::fullPicAddr($v->avatar);
            $v->date = date('m.d H:i',$v->create_time);
        }

        $out['list'] = $list;

        AJAX::success($out);
        
    }


    function message_detail($id,SchoolMessageModel $model){

        if(!$id)die();

        $info = $model->select('id','istag','isread','type','user.avatar','user.name','user.name_en','create_time','message','reply')->find($id);
        

        if(!$info)die();

        if(!$info->isread){
            $model->set(['isread'=>1])->save($id);
        }

        $info->fullAvatar = Func::fullPicAddr($info->avatar);
        $info->date = date('m.d H:i',$info->create_time);

        View::addData(['info'=>$info]);

        View::hamlReader('Headmaster/'.__FUNCTION__,'App');

    }


    function mark($id,SchoolMessageModel $model){

        if(!$id)AJAX::error('no data');

        $info = $model->find($id);

        $flag = $info->istag?'0':'1';

        $info = $model->set(['istag'=>$flag])->save($id);
        AJAX::success(['flag'=>$flag]);

    }

    function reply($id,SchoolMessageModel $model,$reply){

        if(!$id)AJAX::error('no data');

        $info = $model->find($id);
        if(!$info)AJAX::error('no data');

        $info->reply = $reply;

        $info->save();

        AJAX::success();
        
    }
}