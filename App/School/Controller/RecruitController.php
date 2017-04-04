<?php

namespace App\School\Controller;


use Controller;
use View;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;

use App\School\Model\RecruitModel;
use App\School\Model\RecruitStudentsModel;

class RecruitController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

        !$this->L->id && AJAX::error_i18n('not_login');

    }

    function get($id,RecruitModel $model){

        $recruit = $model->find($id);
        !$recruit && AJAX::error_i18n('no_data');

        AJAX::success(['info'=>$recruit]);
    }

    function add($name,$time,$address,$number,$comment,RecruitModel $model){

        $data = Request::getInstance()->post(['title','date','time','address','number','comment']);
        count($data) != 6 && AJAX::error_i18n('param_error');

        $data['create_user'] = $data['update_user'] = $this->L->id;
        $data['create_time'] = $data['update_time'] = TIME_NOW;

        $succ = $model->set($data)->add()->getStatus();
        !$succ && AJAX::error_i18n('param_error');
        
        AJAX::success();

    }

    function lists($page = 1,$limit = 30,RecruitModel $model){
        
        $name = $this->L->i18n->language == 'cn' ? 'user.name' : 'user.name_en>name';

        $out['list'] = $model->page($page,$limit)->selectExcept('comment')->select('*',$name)->get()->toArray();
        $out['count'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);

    }

    function change($id,$status = null,RecruitModel $model,$title,$number,$address,$comment,$date){

        $recruit = $model->find($id);
        !$recruit && AJAX::error_i18n('no_data');

        $status !== null && $recruit->status = $status ? 1 : 0;
        $title !== null && $recruit->title = $title;
        $number !== null && $recruit->number = $number;
        $time !== null && $recruit->time = $time;
        $date !== null && $recruit->date = $date;
        $address !== null && $recruit->address = $address;
        $comment !== null && $recruit->comment = $comment;

        $recruit->update_user = $this->L->id;
        $recruit->update_time = TIME_NOW;
        !$recruit->save()->getStatus() && AJAX::error_i18n('save_failed');

        AJAX::success();

    }

    function schange($id = 0,RecruitStudentsModel $model){

        $recruit = $model->find($id);
        !$recruit && AJAX::error_i18n('no_data');

        $data = Request::getInstance()->post(['parent_name','parent_name_en','student_name','student_name_en','address','age','phone']);
        $data['update_time'] = TIME_NOW;
        
        !$model->set($data)->where(['id'=>$id])->save()->getStatus() && AJAX::error_i18n('save_failed');

        AJAX::success();

    }


    function del($id = 0,RecruitModel $model){

        $recruit = $model->remove($id);
        AJAX::success();

    }


    function slists($page = 1,$limit = 30,RecruitStudentsModel $model){

        $pname = $this->L->i18n->language == 'cn' ? 'parent_name' : 'parent_name_en>parent_name';
        $sname = $this->L->i18n->language == 'cn' ? 'student_name' : 'student_name_en>student_name';
        $out['list'] = $model->page($page,$limit)->select('id',$sname,$pname,'phone','age','recruit_id','create_time')->get()->toArray();
        $out['count'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);

    }

    function sget($id,RecruitStudentsModel $model){

        $recruit = $model->find($id);
        !$recruit && AJAX::error_i18n('no_data');

        AJAX::success(['info'=>$recruit]);
    }


    function sdel($id = 0,RecruitStudentsModel $model){

        $recruit = $model->remove($id);
        AJAX::success();

    }

}