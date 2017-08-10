<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;
use DB;
# Model
use App\Lawyer\Model\VisaWorkModel;
use App\Lawyer\Model\VisaFamilyModel;
use App\Lawyer\Model\VisaRefuseModel;
use App\Lawyer\Model\VisaTravelModel;
use App\Lawyer\Model\VisaMarryModel;
use App\Lawyer\Model\VisaGraduateModel;
use App\Lawyer\Model\VisaStudentModel;
use App\Lawyer\Model\ConsultPayRuleModel;
use App\Lawyer\Model\VisaPerpetualModel;
use App\Lawyer\Model\VisaSelectModel;
use App\Lawyer\Model\VisaSelectOptionModel;
use App\Lawyer\Model\VisaTechnologyModel;
use App\Lawyer\Model\VisaTechnologyOptionModel;
use App\Lawyer\Model\VisaBusinessModel;
use App\Lawyer\Model\VisaBusinessOptionModel;


use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\H5Model;

class VisaController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }


    function submit($lawyer_id,$type){
        
    }

    # 工作签证
    function getWork(VisaWorkModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updWork(VisaWorkModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }

    # 家庭团聚签证
    function getFamily(VisaFamilyModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updFamily(VisaFamilyModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }


    # 拒签上诉
    function getRefuse(VisaRefuseModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updRefuse(VisaRefuseModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }

    # 旅游签证
    function getTravel(VisaTravelModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updTravel(VisaTravelModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }


    # 配偶签证
    function getMarry(VisaMarryModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updMarry(VisaMarryModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;

        // $paths = Func::uploadFiles();
        
        // $data['pic'] = implode(',',$paths);
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }


    # 学生毕业签证
    function getGraduate(VisaGraduateModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updGraduate(VisaGraduateModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }


    # 学生签证/陪读
    function getStudent(VisaStudentModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updStudent(VisaStudentModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }

    # 永久签证
    function getPerpetual(VisaPerpetualModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        AJAX::success($out);
    }
    function updPerpetual(VisaPerpetualModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        
        AJAX::success($out);
        

    }

    # 技术移民签证
    function getTechnology(
        VisaTechnologyModel $model,
        VisaTechnologyOptionModel $optionModel,
        VisaSelectModel $visaSelectModel
        ){

        !$this->L->id && AJAX::error('未登录');
        $info = $model->find($this->L->id);

        if($info){

            $option = $optionModel->where(['technology_id'=>$info->id])->get('select_id')->toArray();
        }

        $select = $visaSelectModel->select([
            'id,name,GROUP_CONCAT(%F) AS `option`','option.name'
        ],'RAW')->where(['type'=>1])->group('id')->order('ord')->get()->toArray();

        foreach($select as &$v){
            $v->option = explode(',',$v->option);
            if($info){
                $v->value = $option[$v->id]->value;
                if(!$v->value)$v->value = '';
            }else{
                $v->value = '';
            }
        }

        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        $out['select'] = $select;
        AJAX::success($out);

    }
    function updTechnology(
        VisaTechnologyModel $model,
        VisaTechnologyOptionModel $optionModel,
        $json
        ){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data2['id'] = $this->L->id;
        $data2['update_time'] = TIME_NOW;

        DB::start();
        
        $model->set($data2)->add(true);

        $optionModel->where(['technology_id'=>$this->L->id])->remove();

        $obj = json_decode($json);
        !$obj && AJAX::error('json参数格式错误！');
        
        foreach($obj as $k=>$v){

            $data['technology_id'] = $this->L->id;
            $data['select_id'] = $k;
            $data['value'] = $v;
            $optionModel->set($data)->add();
        }

        DB::commit();

        
        AJAX::success();
        

    }

    # 商业签证
    function getBusiness(
        VisaBusinessModel $model,
        VisaBusinessOptionModel $optionModel,
        VisaSelectModel $visaSelectModel
        ){

        !$this->L->id && AJAX::error('未登录');
        $info = $model->find($this->L->id);

        if($info){

            $option = $optionModel->where(['technology_id'=>$info->id])->get('select_id')->toArray();
        }

        $select = $visaSelectModel->select([
            'id,name,GROUP_CONCAT(%F) AS `option`','option.name'
        ],'RAW')->where(['type'=>2])->group('id')->order('ord')->get()->toArray();

        foreach($select as &$v){
            $v->option = explode(',',$v->option);
            if($info){
                $v->value = $option[$v->id]->value;
                if(!$v->value)$v->value = '';
            }else{
                $v->value = '';
            }
        }

        $info = AdminFunc::get($model,$this->L->id);

        $out['info'] = $info;
        $out['select'] = $select;
        AJAX::success($out);

    }
    function updBusiness(VisaBusinessModel $model,VisaBusinessOptionModel $optionModel){


        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data2['id'] = $this->L->id;
        $data2['update_time'] = TIME_NOW;

        DB::start();
        
        $model->set($data2)->add(true);

        $optionModel->where(['technology_id'=>$this->L->id])->remove();

        $obj = json_decode($json);
        !$obj && AJAX::error('json参数格式错误！');
        
        foreach($obj as $k=>$v){

            $data['technology_id'] = $this->L->id;
            $data['select_id'] = $k;
            $data['value'] = $v;
            $optionModel->set($data)->add();
        }

        DB::commit();

        
        AJAX::success();
        

    }
    
    private function _getVisa(&$data,$model,$type){

        if($info = $model->select('id','update_time')->find($this->L->id)){
            $info->type = $type;
            $g['k'.$info->update_time] = $info;
            $data = array_merge($data,$g);
            

        }
    
    
    }
    function getVisa(){

        !$this->L->id && AJAX::error('未登录');

        $data = [];

        $this->_getVisa($data,VisaWorkModel::copyMutiInstance(),'work');
        $this->_getVisa($data,VisaFamilyModel::copyMutiInstance(),'family');
        $this->_getVisa($data,VisaRefuseModel::copyMutiInstance(),'refuse');
        $this->_getVisa($data,VisaTravelModel::copyMutiInstance(),'travel');
        $this->_getVisa($data,VisaMarryModel::copyMutiInstance(),'marry');
        $this->_getVisa($data,VisaGraduateModel::copyMutiInstance(),'graduate');
        $this->_getVisa($data,VisaStudentModel::copyMutiInstance(),'student');
        $this->_getVisa($data,VisaPerpetualModel::copyMutiInstance(),'perpetual');
        $this->_getVisa($data,VisaTechnologyModel::copyMutiInstance(),'technology');
        $this->_getVisa($data,VisaBusinessModel::copyMutiInstance(),'business');

        krsort($data);

        $out['list'] = array_values( $data );

        AJAX::success($out);
        

    }







    function admin_limit_get(ConsultPayRuleModel $model,$id){

        $this->L->adminPermissionCheck(70);

        $names = ['','法律会员','留学转学会员','签证会员'];

        $name = $names[$id];

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_limit_get',
                'upd'   => '/visa/admin_limit_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '多少小时之后没回复可切换律师',
                    'name'  =>  'hours',
                    'size'  =>  '2'
                ],
                [
                    'title' =>  '总字数',
                    'name'  =>  'word_count',
                    'size'  =>  '2',
                    'description'=>'*-1为无限'
                ],
                [
                    'title' =>  '总问题数',
                    'name'  =>  'question_count',
                    'size'  =>  '2',
                    'description'=>'*-1为无限'
                ],
                [
                    'title' =>  '有效期限（天）',
                    'name'  =>  'expiry',
                    'size'  =>  '2',
                    'description'=>'*-1为无限'
                ],
                [
                    'title' =>  '会员金额',
                    'name'  =>  'price',
                    'size'  =>  '2',
                    'default'=> '0.00',
                ],
                [
                    'title' =>  '会员权益',
                    'name'  =>  'content',
                    'type'  =>  'h5'
                ],
                
                
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $info->content = H5Model::copyMutiInstance()->find($id+2)->content;

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_limit_upd(ConsultPayRuleModel $model,$id,$pwd,$content){
        $this->L->adminPermissionCheck(70);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);
        unset($data['content']);

        H5Model::copyMutiInstance()->set(['content'=>$content])->save($id+2);


        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }

}