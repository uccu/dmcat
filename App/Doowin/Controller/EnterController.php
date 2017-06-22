<?php

namespace App\Doowin\Controller;


use App\Doowin\Model\StaticPageModel;
use App\Doowin\Model\IntroductionProductModel;
use App\Doowin\Model\DevelopModel;
use App\Doowin\Model\ChairmanPictureModel;
use App\Doowin\Model\HonorModel;
use App\Doowin\Model\CharitableModel;
use App\Doowin\Model\MagazineModel;


use Controller;
use Request;
use AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use Model;

class EnterController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

    }


    # 集团简介
        function introduction_get(StaticPageModel $model){
            $out['info'] = $model->find(1);
            AJAX::success($out);
        }
        function introduction_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(1);
            AJAX::success();
        }
        function introduction_product_lists(IntroductionProductModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/enter/introduction_product_get',
                'upd'=>'enter/introduction_product_detail',
                'del'=>'/enter/introduction_product_del'
            ];

            $out['thead'] = [
                'ID'=>['class'=>'tc'],
                '标题'=>['class'=>'tc'],
                '优先级'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'id'    =>['class'=>'tc'],
                'title' =>['class'=>'tc'],
                'top'   =>['class'=>'tc'],
                '_opt'  =>['class'=>'tc','updateLink'=>1],
            ];

            if($year)$where['year'] = $year;

            $list = $model->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();

            foreach($list as &$v){


            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function introduction_product_get(IntroductionProductModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $out['info'] = $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            AJAX::success($out);

        }
        function introduction_product_upd($id,IntroductionProductModel $model){

            $data = Request::getInstance()->request(['title','title_en','content_en','top','content','pic']);
            unset ($data['id']);
            $data['top'] = floor($data['top']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function introduction_product_del($id,IntroductionProductModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }

    # 董事长专区
        function chairman_get(StaticPageModel $model){
            $out['info'] = $model->find(2);
            AJAX::success($out);
        }
        function chairman_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(2);
            AJAX::success();
        }

        function chairman_picture_lists(ChairmanPictureModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/enter/chairman_picture_get',
                'upd'=>'/enter/chairman_picture_upd',
                'del'=>'/enter/chairman_picture_del'
            ];

            $out['thead'] = [
                '描述'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'description'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];

            $list = $model->where($where)->page($page,$limit)->order('year desc','month desc')->get()->toArray();

            foreach($list as &$v){

            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function chairman_picture_get(ChairmanPictureModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            $info->picArray = [];
            $info->pic2Array = [];
            if($info->pic){
                $pics = explode(';',$info->pic);
                $info->pic2Array = $pics;
                foreach($pics as &$v)$v = Func::fullPicAddr( $v );
                $info->picArray = $pics;
            }
            $out['info'] = $info;
            AJAX::success($out);

        }
        function chairman_picture_upd($id,ChairmanPictureModel $model){

            $data = Request::getInstance()->request(['pic','description','description_en']);
            $data['year'] = floor($data['year']);
            $data['month'] = floor($data['month']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function chairman_picture_del($id,ChairmanPictureModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }
        
    # 发展历程
        function develop_lists(DevelopModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/enter/develop_get',
                'upd'=>'/enter/develop_upd',
                'del'=>'/enter/develop_del'
            ];

            $out['thead'] = [
                '年份'=>['class'=>'tc'],
                '月份'=>['class'=>'tc'],
                '标题'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'year'=>['class'=>'tc'],
                'month'=>['class'=>'tc'],
                'title'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];

            $list = $model->where($where)->page($page,$limit)->order('year desc','month desc')->get()->toArray();

            foreach($list as &$v){

            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function develop_get(DevelopModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $out['info'] = $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            AJAX::success($out);

        }
        function develop_upd($id,DevelopModel $model){

            $data = Request::getInstance()->request(['year','title','title_en','month','description','description_en']);
            $data['year'] = floor($data['year']);
            $data['month'] = floor($data['month']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function develop_del($id,DevelopModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }
    
    # 企业文化
        function culture_get(StaticPageModel $model){
            $out['info'] = $model->find(3);
            AJAX::success($out);
        }
        function culture_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(3);
            AJAX::success();
        }

        function magazine_lists(MagazineModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/enter/magazine_get',
                'upd'=>'/enter/magazine_upd',
                'del'=>'/enter/magazine_del'
            ];

            $out['thead'] = [
                '年份'=>['class'=>'tc'],
                '标题'=>['class'=>'tc'],
                '副标题'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'year'=>['class'=>'tc'],
                'title'=>['class'=>'tc'],
                'small'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];

            $list = $model->where($where)->page($page,$limit)->order('year desc','top desc')->get()->toArray();

            foreach($list as &$v){

            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function magazine_get(MagazineModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $out['info'] = $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            AJAX::success($out);

        }
        function magazine_upd($id,MagazineModel $model){

            $data = Request::getInstance()->request(['year','pic','title','title_en','red','red_en','small','small','top','description','description_en']);
            $data['year'] = floor($data['year']);
            $data['top'] = floor($data['top']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function magazine_del($id,MagazineModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }

    # 企业荣誉
        function honor_lists(HonorModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/enter/honor_get',
                'upd'=>'/enter/honor_upd',
                'del'=>'/enter/honor_del'
            ];

            $out['thead'] = [
                '年份'=>['class'=>'tc'],
                '月份'=>['class'=>'tc'],
                '描述'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'year'=>['class'=>'tc'],
                'month'=>['class'=>'tc'],
                'description'=>['class'=>'tc'],
                '_opt'=>['class'=>'tc'],
            ];

            $list = $model->where($where)->page($page,$limit)->order('year desc','month desc')->get()->toArray();

            foreach($list as &$v){

            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function honor_get(HonorModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $out['info'] = $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            AJAX::success($out);

        }
        function honor_upd($id,HonorModel $model){

            $data = Request::getInstance()->request(['year','month','description','description_en']);
            $data['year'] = floor($data['year']);
            $data['month'] = floor($data['month']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function honor_del($id,HonorModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }
    # 社会责任
        function responsibility_get(StaticPageModel $model){
            $out['info'] = $model->find(4);
            AJAX::success($out);
        }
        function responsibility_upd(StaticPageModel $model){
            $data = Request::getInstance()->request(['content','content_en']);
            $model->set($data)->save(4);
            AJAX::success();
        }

        # charitable
        function charitable_lists(CharitableModel $model,$page = 1,$limit = 30){

            $out = [
                'get'=>'/enter/charitable_get',
                'upd'=>'/enter/charitable_upd',
                'del'=>'/enter/charitable_del'
            ];

            $out['thead'] = [
                '日期'=>['class'=>'tc'],
                '图片'=>['class'=>'tc'],
                
                '_opt'=>['class'=>'tc'],
            ];
            
            $out['tbody'] = [
                'date'=>['class'=>'tc'],
                'pic'=>['type'=>'imga','class'=>'tc'],
                
                '_opt'=>['class'=>'tc'],
            ];

            $list = $model->where($where)->page($page,$limit)->order('year desc','month desc')->get()->toArray();

            foreach($list as &$v){

                $v->pic = '/pic/'.$v->pic;
                $v->date = $v->year.'年'.$v->month.'月';
            }

            $out['max'] = $model->where($where)->select('COUNT(*) as c','RAW')->find()->c;
            $out['page'] = $page;
            $out['limit'] = $limit;

            $out['list']  = $list;
            AJAX::success($out);

        }
        function charitable_get(CharitableModel $model,$id){

            !$id && AJAX::success(['info'=>[]]);
            $out['info'] = $info = $model->find($id);
            !$info && AJAX::error('没有数据！');
            AJAX::success($out);

        }
        function charitable_upd($id,CharitableModel $model){

            $data = Request::getInstance()->request(['description','description_en','pic','month','year']);
            unset ($data['id']);
            $data['year'] = floor($data['year']);
            $data['month'] = floor($data['month']);
            if(!$id)$id = $model->set($data)->add()->getStatus();
            else $model->set($data)->save($id);

            AJAX::success();

        }
        function charitable_del($id,CharitableModel $model){

            !$id && AJAX::error('删除失败！');
            $model->remove($id);
            AJAX::success();

        }
}
