<?php

namespace App\School\Controller;


use App\School\Model\UserModel;
use App\School\Model\UserSchoolModel;
use App\School\Model\UserClassesModel;
use App\School\Model\UserStudentModel;
use App\School\Model\StudentModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;
use App\School\Tool\Func;
use Model;

class StaffController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;
        $this->salt = $this->L->config->site_salt;

    }


    /* 列表 */
    function lists(UserModel $model,$type = 0,$search = '',$page = 1,$school,$limit = 50){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/staff/get','upd'=>'/staff/upd?type='.$type,'del'=>'/staff/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->adminIndex->name)=>['class'=>'tc'],
            ($this->lang->adminLogin->user)=>['class'=>'tc'],
            ($this->lang->user->email)=>['class'=>'tc'],
            ($this->lang->user->phone)=>['class'=>'tc'],
            
            '_opt'=>['class'=>'tc'],
        ];
        
        $name = $this->lang->language == 'cn' ? 'name' : 'name_en';

        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            $name=>['class'=>'tc'],
            'user_name'=>['class'=>'tc'],
            'email'=>['class'=>'tc'],
            'phone'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        
        
        $out['lang'] = $this->lang->language;

        $where['type'] = $type;
        if($search)$where[] = ['name LIKE %n OR name_en LIKE %n OR user_name LIKE %n OR phone LIKE %n OR email LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%'];

        


        $list = $model->where($where)->page($page,$limit)->get()->toArray();

        $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;

        $out['list']  = $list;
        AJAX::success($out);


    }

    function get($id,UserModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->select('*','school.school_id','classes.classes_id')->find($id);
        $out['student'] = UserStudentModel::getInstance()->select('id','studentInfo.name','studentInfo.name_en')->where(['user_id'=>$id])->get()->toArray();
        !$info && AJAX::error_i18n('no_data');
        $out['lang'] = $this->lang->language;

        AJAX::success($out);

    }

    function upd($id,UserModel $model,$school_id,$classes_id){

        $data = Request::getInstance()->request(['name','name_en','email','avatar','phone','user_name','type','raw_password','avatar']);
        unset ($data['id']);

        if(!$id){

            $data['password'] = sha1($this->salt.md5($data['raw_password']));
            $data['create_time'] = TIME_NOW;
            $id = $model->set($data)->add()->getStatus();
            Model::getInstance('user_online')->set(['id'=>$id])->add();

        }else{
            $info = $model->find($id);
            !$info && AJAX::error_i18n('no_user_exist');

            if($data['raw_password'] != $info->raw_passowrd)$data['password'] = sha1($this->salt.md5($data['raw_password']));

            $model->set($data)->save($id);
        }

        if($school_id){
            $mmm = UserSchoolModel::getInstance()->where(['user_id'=>$id])->find();
            if($mmm){
                $mmm->school_id = $school_id;
                $mmm->save();
            }else{
                UserSchoolModel::getInstance()->set(['school_id'=>$school_id,'user_id'=>$id])->add();
            }
        }

        if($classes_id){
            $mmm = UserClassesModel::getInstance()->where(['user_id'=>$id])->find();
            if($mmm){
                $mmm->classes_id = $classes_id;
                $mmm->save();
            }else{
                UserClassesModel::getInstance()->set(['classes_id'=>$classes_id,'user_id'=>$id])->add();
            }
        }

        
        

        AJAX::success();

    }

    function upPic(){

        $out['path'] = Func::uploadFiles('file',100,100);
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);
    }


    function del($id,UserModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        Model::getInstance('user_online')->remove($id);
        Model::getInstance('user_school')->where(['user_id'=>$id])->remove();
        Model::getInstance('user_classes')->where(['user_id'=>$id])->remove();
        Model::getInstance('user_student')->where(['user_id'=>$id])->remove();
        AJAX::success();

    }

    function add_student($student_id,$id){

        $model = UserStudentModel::getInstance();
        $model->getStudent($student_id,$id) && AJAX::error_i18n('is_binding');
        $out['id'] = $model->addStudent($student_id,$id)->getStatus();
        $stu = StudentModel::getInstance()->find($student_id);
        $out['name'] = $this->lang->language == 'cn' ? $stu->name : $stu->name_en;
        AJAX::success($out);
    }

    function delete_student($id){
        
         UserStudentModel::getInstance()->remove($id);
         AJAX::success();
    }


    


}
