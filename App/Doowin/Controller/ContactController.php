<?php

namespace App\Doowin\Controller;


use App\Doowin\Model\StaticPageModel;
use App\Doowin\Model\RecruitModel;
use App\Doowin\Model\RecruitTypeModel;



use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

class ContactController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

    }


    # 德汇招聘
        
        function recruit_lists(RecruitModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/contact/recruit_get',
                'upd'=>'contact/recruit_detail',
                'del'=>'/contact/recruit_del'
            ];

            $out['thead'] = [
                '名称'=>['class'=>'tc'],
                '人数'=>['class'=>'tc'],
                '重要'=>['class'=>'tc'],
                '发布时间'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [

                'name' =>['class'=>'tc'],
                'num'=>['class'=>'tc'],
                'top'   =>['class'=>'tc',"type"=>"checkbox"],
                'time'=>['class'=>'tc'],
                '_opt'  =>['class'=>'tc','updateLink'=>1],
            ];


            $list = $model->where($where)->page($page,$limit)->order('top desc','time desc')->get()->toArray();

            foreach($list as &$v){


            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function recruit_get(RecruitModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $out['info'] = $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            AJAX::success($out);

        }
        function recruit_upd($id,RecruitModel $model){

            $data = Request::getInstance()->request([
                'name','name_en',
                'top',
                'address','address_en',
                'edu','edu_en',
                'typein','typein_en',
                'time',
                'experience','experience_en',
                'type','num',
                'content','content_en',
                ]);
            unset ($data['id']);
            if(isset($data['top']))$data['top'] = floor($data['top']);
            if(isset($data['num']))$data['num'] = floor($data['num']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function recruit_del($id,RecruitModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }

        /* 视频分类 */
        function recruit_type_lists(RecruitTypeModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/news/recruit_type_get',
                'upd'=>'/news/recruit_type_upd',
                'del'=>'/news/recruit_type_del'
            ];

            $out['thead'] = [
                '顺序'=>['class'=>'tc'],
                '名字'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'ord'=>['class'=>'tc'],
                'name'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];

            $list = $model->where($where)->page($page,$limit)->order('ord','id')->get()->toArray();

            foreach($list as &$v){

            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function recruit_type_get(RecruitTypeModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $out['info'] = $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            AJAX::success($out);

        }
        function recruit_type_upd($id,RecruitTypeModel $model){

            $data = Request::getInstance()->request(['name_en','name','ord']);
            unset ($data['id']);
            $data['ord'] = floor($data['ord']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function recruit_type_del($id,RecruitTypeModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }

    # 法律声明
        function notice_get(StaticPageModel $model){
            $out['info'] = $model->find(11);
            AJAX::success($out);
        }
        function notice_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(11);
            AJAX::success();
        }
    
}
