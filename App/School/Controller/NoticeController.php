<?php

namespace App\School\Controller;


use Controller;
use Request;
use App\School\Model\UserModel;
use App\School\Model\StudentModel;
use App\School\Model\NoticeModel;
use App\School\Model\VoteModel;
use App\School\Model\ActivityModel;
use App\School\Model\PropagandaModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;

class NoticeController extends Controller{


    private $L;


    function __construct(){

        $this->L = L::getInstance();
        

    }


    function get($id=0 ,NoticeModel $model){

        !$id && AJAX::success(['info'=>[]]);

        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');

        AJAX::success(['info'=>$info]);

    }
    function lists(NoticeModel $model,$page = 1 ,$limit = 30){

        $out = ['get'=>'/notice/get','upd'=>'/notice/upd','del'=>'/notice/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            "标题/Title"=>['class'=>'tc'],
            "显示/Show"=>['class'=>'tc'],
            "回执/Reply"=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'title'=>['class'=>'tc'],
            'isshow'=>['class'=>'tc eisshow','type'=>'checkbox'],
            'need_confirm'=>['class'=>'tc eneed_confirm','type'=>'checkbox'],
            '_opt'=>['class'=>'tc'],
        ];


        $out['lang'] = $this->lang->language;

        $list = $model->page($page,$limit)->order('id','DESC')->get()->toArray();

        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);
    }
    function del($id = 0,NoticeModel $model){

        !$this->L->id && AJAX::error_i18n('not_login');

        $model->remove($id);
        AJAX::success();

    }
    function upd($id = 0,NoticeModel $model){

        $data = Request::getInstance()->request($model->field);

        if($id){

            $info = $model->find($id);
            !$info && AJAX::error_i18n('no_data');

            !$model->set($data)->save($id)->getStatus() && AJAX::error_i18n('save_failed');

        }else{
            
            $data['create_time'] = TIME_NOW;

            unset($data['id']);
            
            $model->set($data)->add()->getStatus();

        }

        

        AJAX::success();
        
    }

    function vote_lists(VoteModel $model,$page = 1 ,$limit = 30){

        $out = ['get'=>'/notice/vote_get','upd'=>'/notice/vote_upd','del'=>'/notice/vote_del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            "标题/Title"=>['class'=>'tc'],
            "显示/Show"=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'title'=>['class'=>'tc'],
            'isshow'=>['class'=>'tc eisshow','type'=>'checkbox'],
            '_opt'=>['class'=>'tc'],
        ];


        $out['lang'] = $this->lang->language;

        $list = $model->page($page,$limit)->order('id','DESC')->get()->toArray();


        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);
    }
    function vote_upd($id = 0,VoteModel $model,$option){

        $data = Request::getInstance()->request($model->field);

        if($option)$data['options'] = implode(';',$option);

        if($id){

            $info = $model->find($id);
            !$info && AJAX::error_i18n('no_data');
            !$model->set($data)->save($id)->getStatus() && AJAX::error_i18n('save_failed');
        }else{
            
            $data['create_time'] = TIME_NOW;
            unset($data['id']);
            $model->set($data)->add()->getStatus();
        }
        AJAX::success();
        
    }
    function vote_get($id=0 ,VoteModel $model){
        !$id && AJAX::success(['info'=>[]]);
        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');
        $info->option = explode(';',$info->options);
        AJAX::success(['info'=>$info]);
    }



    function activity_get($id=0 ,ActivityModel $model){
        !$id && AJAX::success(['info'=>[]]);
        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');
        AJAX::success(['info'=>$info]);
    }
    function activity_lists(ActivityModel $model,$page = 1 ,$limit = 30){
        $out = ['get'=>'/notice/activity_get','upd'=>'/notice/activity_upd','del'=>'/notice/activity_del'];
        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            "标题/Title"=>['class'=>'tc'],
            "显示/Show"=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'title'=>['class'=>'tc'],
            'isshow'=>['class'=>'tc eisshow','type'=>'checkbox'],
            '_opt'=>['class'=>'tc'],
        ];
        $out['lang'] = $this->lang->language;
        $list = $model->page($page,$limit)->order('id','DESC')->get()->toArray();
        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);
    }
    function activity_del($id = 0,ActivityModel $model){
        !$this->L->id && AJAX::error_i18n('not_login');
        $model->remove($id);
        AJAX::success();
    }
    function activity_upd($id = 0,ActivityModel $model){
        $data = Request::getInstance()->request($model->field);
        if($id){
            $info = $model->find($id);
            !$info && AJAX::error_i18n('no_data');
            !$model->set($data)->save($id)->getStatus() && AJAX::error_i18n('save_failed');
        }else{
            $data['create_time'] = TIME_NOW;
            unset($data['id']);
            $model->set($data)->add()->getStatus();
        }
        AJAX::success();
        
    }

    function propaganda_get($id=0 ,PropagandaModel $model){
        !$id && AJAX::success(['info'=>[]]);
        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');
        AJAX::success(['info'=>$info]);
    }
    function propaganda_lists(PropagandaModel $model,$page = 1 ,$limit = 30){
        $out = ['get'=>'/notice/activity_get','upd'=>'/notice/activity_upd','del'=>'/notice/activity_del'];
        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            "标题/Title"=>['class'=>'tc'],
            "显示/Show"=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'title'=>['class'=>'tc'],
            'isshow'=>['class'=>'tc eisshow','type'=>'checkbox'],
            '_opt'=>['class'=>'tc'],
        ];
        $out['lang'] = $this->lang->language;
        $list = $model->page($page,$limit)->order('id','DESC')->get()->toArray();
        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);
    }
    function propaganda_del($id = 0,PropagandaModel $model){
        !$this->L->id && AJAX::error_i18n('not_login');
        $model->remove($id);
        AJAX::success();
    }
    function propaganda_upd($id = 0,PropagandaModel $model){
        $data = Request::getInstance()->request($model->field);
        if($id){
            $info = $model->find($id);
            !$info && AJAX::error_i18n('no_data');
            !$model->set($data)->save($id)->getStatus() && AJAX::error_i18n('save_failed');
        }else{
            $data['create_time'] = TIME_NOW;
            unset($data['id']);
            $model->set($data)->add()->getStatus();
        }
        AJAX::success();
        
    }



    


    function get_notice_lists(NoticeModel $model){
        $out['list'] = $model->select('*','user.avatar','user.name','user.name_en')->where(['isshow'=>1])->order('id','DESC')->get()->toArray();
        AJAX::success($out);
    }
    function get_notice_info(NoticeModel $model,$id = 0){
        $out['info'] = $model->find($id);
        if(!$out['info'])AJAX::error('没有数据/no data');
        AJAX::success($out);
    }


    function get_vote_lists(VoteModel $model){
        $out['list'] = $model->select('*','user.avatar','user.name','user.name_en')->where(['isshow'=>1])->order('id','DESC')->get()->toArray();
        foreach($out['list'] as &$v){
            $v->fullAvatar = Func::fullPicAddr($v->avatar);
        }
        AJAX::success($out);
    }
    function get_vote_info(VoteModel $model,$id = 0){
        $out['info'] = $model->find($id);
        if(!$out['info'])AJAX::error('没有数据/no data');
        AJAX::success($out);
    }


    function get_activity_lists(ActivityModel $model){
        $out['list'] = $model->select('*','user.avatar','user.name','user.name_en')->where(['isshow'=>1])->order('id','DESC')->get()->toArray();
        AJAX::success($out);
    }
    function get_activity_info(ActivityModel $model,$id = 0){
        $out['info'] = $model->find($id);
        if(!$out['info'])AJAX::error('没有数据/no data');
        AJAX::success($out);
    }



    function get_propaganda_lists(PropagandaModel $model){
        $out['list'] = $model->select('*','user.avatar','user.name','user.name_en')->where(['isshow'=>1])->order('id','DESC')->get()->toArray();
        AJAX::success($out);
    }
    function get_propaganda_info(PropagandaModel $model,$id = 0){
        $out['info'] = $model->find($id);
        if(!$out['info'])AJAX::error('没有数据/no data');
        AJAX::success($out);
    }


}