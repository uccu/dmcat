<?php

namespace App\School\Controller;


use App\School\Model\I18nModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;

class I18nController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }

    function types(I18nModel $model){

        $out['list'] = $model->select('type')->get_field('type',null,1);

        AJAX::success($out);
    }


    /* 列表 */
    function lists(I18nModel $model,$type = 0,$search = '',$page = 1,$limit = 50){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/i18n/get','upd'=>'/i18n/upd','del'=>'/i18n/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            'type'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'cn'=>['class'=>'tc'],
            'en'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'type'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'cn'=>['class'=>'tc'],
            'en'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        
        
        $out['lang'] = $this->lang->language;

        $where = [];
        if($search)$where[] = ['type LIKE %n OR name LIKE %n OR cn LIKE %n OR en LIKE %n','%'.$search.'%','%'.$search.'%','%'.$search.'%','%'.$search.'%'];
        elseif($type)$where['type'] = $type;


        $list = $model->where($where)->page($page,$limit)->get()->toArray();
        $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;

        $out['page'] = $page;
        $out['limit'] = $limit;

        $out['list']  = $list;
        AJAX::success($out);


    }

    function get($id,I18nModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

    function upd($id,I18nModel $model){

        $data = Request::getInstance()->request($model->field);
        unset ($data['id']);

        if(!$id){
            
            
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }


    function del($id,I18nModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }


    


}
