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
use App\School\Model\NoticeConfirmModel;
use App\School\Model\ActivityConfirmModel;
use App\School\Model\VoteConfirmModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;

class NoticeController extends Controller{


    private $L;


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    function get($id=0 ,NoticeModel $model){

        !$id && AJAX::success(['info'=>[]]);

        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');

        AJAX::success(['info'=>$info]);

    }
    function lists(NoticeModel $model,$page = 1 ,$limit = 30,$need_confirm = null){

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

        if($need_confirm)$where['need_confirm'] = 1;

        $list = $model->where($where)->page($page,$limit)->order('id','DESC')->get()->toArray();

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
    function vote_upd($id = 0,VoteModel $model,$option,$end_date){

        $data = Request::getInstance()->request($model->field);

        $option = Request::getInstance()->post('option','raw');

        if($option)$data['options'] = implode(';',$option);

        if($end_date)$data['end_time'] = strtotime($end_date);

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
        if($info->end_time)$info->end_date = date('Y-m-d',$info->end_time);
        $info->option = explode(';',$info->options);
        AJAX::success(['info'=>$info]);
    }



    function activity_get($id=0 ,ActivityModel $model){
        !$id && AJAX::success(['info'=>[]]);
        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');
        if($info->end_time)$info->end_date = date('Y-m-d',$info->end_time);
        $info->option = explode(';',$info->options);
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
    function activity_upd($id = 0,ActivityModel $model,$option,$end_date){

        $data = Request::getInstance()->request($model->field);
        $option = Request::getInstance()->post('option','raw');

        if($option)$data['options'] = implode(';',$option);

        if($end_date)$data['end_time'] = strtotime($end_date);
        
        
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
        $out = ['get'=>'/notice/propaganda_get','upd'=>'/notice/propaganda_upd','del'=>'/notice/propaganda_del'];
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
            $v->date = date('m.d H:i',$v->create_time);
            $v->end_date = date('Y-m-d H:i:s',$v->end_time);
            $v->fullAvatar = Func::fullPicAddr($v->avatar);
        }
        AJAX::success($out);
    }
    function get_vote_info(VoteModel $model,$id = 0,$student_id = 0,VoteConfirmModel $vModel){
        $student_id = Request::getInstance()->cookie('student_id');
        $info = $model->select('*','user.avatar','user.name','user.name_en')->find($id);
        if(!$info)AJAX::error('没有数据/no data');

        $where['vote_id'] = $id;
        $where['student_id'] = $student_id;
        $vote = $vModel->where($where)->find();
        $info->voted = $vote ? $vote->answer : '0';
        $info->date = date('m.d H:i',$info->create_time);
        $info->end_date = date('Y-m-d H:i:s',$info->end_time);
        $info->fullAvatar = Func::fullPicAddr($info->avatar);
        $info->option = explode(';',$info->options);
        $info->content = str_replace("\r\n",'<br>',$info->content);
        $count = $vModel->select('COUNT(*) AS num,answer','RAW')->group('answer')->where(['vote_id'=>$id])->get('answer')->toArray();

       foreach($info->option as $k=>$v){
           $count[$k+1] = $count[$k+1] ? $count[$k+1]->num : '0';
       }
        $countAll = $vModel->select('COUNT(*) AS num','RAW')->where(['vote_id'=>$id])->find()->num;

        $out['info'] = $info; 
        $out['count'] = $count;
        $out['countAll'] = $countAll;
        AJAX::success($out);
    }

    function to_vote($id = 0,$student_id = 0,VoteConfirmModel $model,$answer = '0'){

        $student_id = Request::getInstance()->cookie('student_id');

        $data['vote_id'] = $id;
        $data['student_id'] = $student_id;

        $model->where($data)->find() && AJAX::error('您已经投过票了/Sorry,you\'ve already voted');

        $data['answer'] = $answer;
        $data['create_time'] = TIME_NOW;
        $vote = $model->set($data)->add();
        AJAX::success();
    }


    function get_activity_lists(ActivityModel $model){
        $out['list'] = $model->select('*','user.avatar','user.name','user.name_en')->where(['isshow'=>1])->order('id','DESC')->get()->toArray();
        foreach($out['list'] as &$v){
            $v->date = date('m.d H:i',$v->create_time);
            $v->end_date = date('Y-m-d H:i:s',$v->end_time);
            $v->fullAvatar = Func::fullPicAddr($v->avatar);
        }
        AJAX::success($out);
    }
    function get_activity_info(ActivityModel $model,$id = 0,$student_id = 0,ActivityConfirmModel $aModel){

        $student_id = Request::getInstance()->cookie('student_id');
        $info = $model->select('*','user.avatar','user.name','user.name_en')->find($id);
        if(!$info)AJAX::error('没有数据/no data');

        $where['activity_id'] = $id;
        $where['student_id'] = $student_id;
        $vote = $aModel->where($where)->find();
        $info->voted = $vote ? $vote->answer : '0';
        $info->remark = $vote ? $vote->remark : '0';
        $info->date = date('m.d H:i',$info->create_time);
        $info->end_date = date('Y-m-d H:i:s',$info->end_time);
        $info->fullAvatar = Func::fullPicAddr($info->avatar);
        $info->option = explode(';',$info->options);
        $info->content = str_replace("\r",'<br>',$info->content);
        $count = $aModel->select('COUNT(*) AS num,answer','RAW')->group('answer')->where(['activity_id'=>$id])->get('answer')->toArray();

       foreach($info->option as $k=>$v){
           $count[$k+1] = $count[$k+1] ? $count[$k+1]->num : '0';
       }
        $countAll = $aModel->select('COUNT(*) AS num','RAW')->where(['activity_id'=>$id])->find()->num;

        $out['info'] = $info; 
        $out['count'] = $count;
        $out['countAll'] = $countAll;

        AJAX::success($out);
    }
    function to_activity($id = 0,$student_id = 0,ActivityConfirmModel $model,$answer = '0',$remark = ''){

        $student_id = Request::getInstance()->cookie('student_id');
        $data['activity_id'] = $id;
        $data['student_id'] = $student_id;

        $model->where($data)->find() && AJAX::error('您已经投过票了/Sorry,you\'ve already voted');

        $data['answer'] = $answer;
        $data['remark'] = $remark;
        $data['create_time'] = TIME_NOW;
        $vote = $model->set($data)->add();
        AJAX::success();
    }


    function get_propaganda_lists(PropagandaModel $model){
        $out['list'] = $model->select('*','user.avatar','user.name','user.name_en')->where(['isshow'=>1])->order('id','DESC')->get()->toArray();
        foreach($out['list'] as &$v){
            $v->date = date('m.d H:i',$v->create_time);
            $v->fullAvatar = Func::fullPicAddr($v->avatar);
        }
        AJAX::success($out);
    }
    function get_propaganda_info(PropagandaModel $model,$id = 0){
        $out['info'] = $model->select('*','user.avatar','user.name','user.name_en')->find($id);
        if(!$out['info'])AJAX::error('没有数据/no data');
        $out['info']->date = date('m.d H:i',$out['info']->create_time);
        $out['info']->fullAvatar = Func::fullPicAddr($out['info']->avatar);
        AJAX::success($out);
    }



    /** 通知回执 **/
    function get_notice_confirm(StudentModel $model,NoticeConfirmModel $nModel,$notice_id = 0,$classes_id = 0){

        $out = ['upd'=>'/notice/upd_notice_confirm'];

        $out['thead'] = [
            "学生ID/student's ID"=>['class'=>'tc'],
            "学生名字/student's name"=>['class'=>'tc'],
            "是否回执/has replied"=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name_e'=>['class'=>'tc'],
            'is_confirm'=>['class'=>'tc confirm','type'=>'checkbox'],
            
        ];


        $out['lang'] = $this->lang->language;

        $where['classes_id'] = $classes_id;
        $list = $model->select('id','name','name_en')->where($where)->order('id')->get()->toArray();

        $where['noticeConfirm.notice_id'] = $notice_id;
        $listn = $model->where($where)->get_field('id')->toArray();

        foreach($list as &$v){
            $v->name_e = $v->name . ' ' . $v->name_en;
            $v->is_confirm = in_array($v->id,$listn) ? '1' : '0';
        }

        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);
    }
    function upd_notice_confirm(NoticeConfirmModel $model,$student_id = 0,$notice_id = 0){
        
        $data['student_id'] = $student_id;
        $data['notice_id'] = $notice_id;
        if(!$model->where($data)->find()){
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add(true);
        }else{
            $model->where($data)->remove();
        }
        AJAX::success();
    }


    /** 投票回执 **/
    function get_vote_confirm(StudentModel $model,VoteModel $vModel,$vote_id = 0,$classes_id = 0){

        $out = ['upd'=>'/notice/upd_vote_confirm'];

        $out['thead'] = [
            "学生ID/student's ID"=>['class'=>'tc'],
            "学生名字/student's name"=>['class'=>'tc'],
            // "是否回执/has replied"=>['class'=>'tc'],
            "投票内容/note content"=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name_e'=>['class'=>'tc'],
            // 'is_confirm'=>['class'=>'tc confirm','type'=>'checkbox'],
            'vote_content'=>['class'=>'tc'],
        ];

        $vote = $vModel->find($vote_id);
        if(!$vote)AJAX::error('投票不存在/vote not exist');

        $vote->optionArray = explode(';',$vote->options);



        $out['lang'] = $this->lang->language;

        $where['classes_id'] = $classes_id;
        $list = $model->select('id','name','name_en')->where($where)->order('id')->get()->toArray();


        $where['voteConfirm.vote_id'] = $vote_id;
        $listn = $model->where($where)->get_field('id')->toArray();
        $listr = $model->where($where)->select('voteConfirm.answer','id')->get('id')->toArray();

        foreach($list as &$v){
            $v->name_e = $v->name . ' ' . $v->name_en;
            $v->is_confirm = in_array($v->id,$listn) ? '1' : '0';
            $v->vote_content = $v->is_confirm && $listr[$v->id]->answer>0 ? $vote->optionArray[$listr[$v->id]->answer - 1] : '';

        }

        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);
    }
    function upd_vote_confirm(VoteConfirmModel $model,$student_id = 0,$vote_id = 0){
        
        $data['student_id'] = $student_id;
        $data['vote_id'] = $vote_id;
        if(!$model->where($data)->find()){
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add(true);
        }else{
            $model->where($data)->remove();
        }
        AJAX::success();
    }

    function get_activity_confirm(StudentModel $model,ActivityModel $vModel,$activity_id = 0,$classes_id = 0){

        $out = ['upd'=>'/notice/upd_activity_confirm'];

        $out['thead'] = [
            "学生ID/student's ID"=>['class'=>'tc'],
            "学生名字/student's name"=>['class'=>'tc'],
            // "是否回执/has replied"=>['class'=>'tc'],
            "反馈/confirm"=>['class'=>'tc'],
            "备注/remark"=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name_e'=>['class'=>'tc'],
            // 'is_confirm'=>['class'=>'tc confirm','type'=>'checkbox'],
            'activity_content'=>['class'=>'tc'],
            'remark'=>['class'=>'tc'],
        ];

        $activity = $vModel->find($activity_id);
        if(!$activity)AJAX::error('投票不存在/activity not exist');

        $activity->optionArray = explode(';',$activity->options);



        $out['lang'] = $this->lang->language;

        $where['classes_id'] = $classes_id;
        $list = $model->select('id','name','name_en')->where($where)->order('id')->get()->toArray();


        $where['activityConfirm.activity_id'] = $activity_id;
        $listn = $model->where($where)->get_field('id')->toArray();
        $listr = $model->where($where)->select('activityConfirm.answer','activityConfirm.remark','id')->get('id')->toArray();

        foreach($list as &$v){
            $v->name_e = $v->name . ' ' . $v->name_en;
            $v->is_confirm = in_array($v->id,$listn) ? '1' : '0';
            $v->activity_content = $v->is_confirm && $listr[$v->id]->answer>0 ? $activity->optionArray[$listr[$v->id]->answer - 1] : '';
            $v->remark = $listr[$v->id] ? $listr[$v->id]->remark : '';
        }

        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);
    }

}