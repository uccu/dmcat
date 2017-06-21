<?php

namespace App\Doowin\Controller;


use App\Doowin\Model\UserModel;
use App\Doowin\Model\UploadModel;

use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

class StaffController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->salt = $this->L->config->site_salt;

    }


    /* 列表 */
    function lists(UserModel $model,$type = 0,$search = '',$page = 1,$school,$limit = 30){
        $this->L->check_type(7);
        // !$this->L->id && AJAX::error('not_login');

        $out = ['get'=>'/staff/get','upd'=>'/staff/upd?type='.$type,'del'=>'/staff/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            '昵称'=>['class'=>'tc'],
            '用户名'=>['class'=>'tc'],
            '邮箱'=>['class'=>'tc'],
            '手机号'=>['class'=>'tc'],
            
            '_opt'=>['class'=>'tc'],
        ];
        

        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'user_name'=>['class'=>'tc'],
            'email'=>['class'=>'tc'],
            'phone'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];


        $where['type'] = $type;
        if($search)$where[] = ['name LIKE %n OR user_name LIKE %n OR phone LIKE %n OR email LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%'];

        


        $list = $model->where($where)->page($page,$limit)->get()->toArray();

        $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;

        $out['list']  = $list;
        AJAX::success($out);


    }

    function get($id,UserModel $model){
        $this->L->check_type(7);
        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error('no_data');

        AJAX::success($out);

    }

    function upd($id,UserModel $model,$raw_password,$user_name){
        $this->L->check_type(7);
        $data = Request::getInstance()->request(['name','email','avatar','phone','user_name','type']);
        unset ($data['id']);

        !$data['user_name'] && AJAX::error('登录用户名不能为空！');

        if(!$id){
            $info = $model->where(['user_name'=>$user_name])->find();
            $info && AJAX::error('登录用户名已存在！');

            $salt = $data['salt'] = Func::randWord(6);
            $data['password'] = sha1($this->salt.md5($raw_password).$salt);
            $data['create_time'] = TIME_NOW;
            $data['avatar'] = 'noavatar.png';
            $id = $model->set($data)->add()->getStatus();

        }else{
            $info = $model->find($id);
            !$info && AJAX::error('no_user_exist');

            $info->user_name != $data['user_name']
            && $model->where(['user_name'=>$user_name])->find()
            && AJAX::error('登录用户名已存在！');
            

            if($raw_password)$data['password'] = sha1($this->salt.md5($raw_password).$info->salt);

            $model->set($data)->save($id);
        }


        AJAX::success();

    }

    function upAvatar(){

        $out['path'] = Func::uploadFiles('file',100,100);
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);
    }

    function upPic(){
        
        $out['path'] = Func::uploadFiles('file');
        if(!$out['path'])AJAX::error('no image');
        $out['fpath'] = '/pic/'.$out['path'];
        $out['apath'] = Func::fullPicAddr($out['path']);
        AJAX::success($out);
    }
    function upFile(){
        
        $path = Func::upload('file');
        if(!$path)AJAX::error('no file');

        $data['path'] = $path;
        $id = UploadModel::getInstance()->set($data)->add();

        $out['path'] = $id;
        $out['fpath'] = '/pic/file.jpg';
        $out['apath'] = Func::fullPicAddr('file.jpg');
        AJAX::success($out);
    }


    function del($id,UserModel $model){
        $this->L->check_type(7);
        !$id && AJAX::error('param_error');
        $model->remove($id);
        AJAX::success();

    }

    


}
