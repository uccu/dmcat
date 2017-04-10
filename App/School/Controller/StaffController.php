<?php

namespace App\School\Controller;


use App\School\Model\UserModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;

class StaffController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 列表 */
    function lists(UserModel $model,$type = 0,$page = 1,$limit = 50){

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



        $list = $model->where(['type'=>$type])->page($page,$limit)->get()->toArray();

        $out['max'] = $model->where(['type'=>$type])->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;

        $out['list']  = $list;
        AJAX::success($out);


    }

    function get($id,UserModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

    function upd($id,UserModel $model){

        $data = Request::getInstance()->request(['name','name_en','email','avatar','phone','user_name','type']);
        unset ($data['id']);

        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }


    function del($id,UserModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }


    


}
