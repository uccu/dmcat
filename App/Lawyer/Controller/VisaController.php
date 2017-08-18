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
        $out['type'] = 'work';
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
        $out['type'] = 'family';
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
        $out['type'] = 'refuse';
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
        $out['type'] = 'travel';
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
        $out['type'] = 'marry';
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
        $out['type'] = 'graduate';
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
        $out['type'] = 'student';
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
        $out['type'] = 'perpetual';
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

        $out['type'] = 'technology';
        AJAX::success($out);
        

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

            $option = $optionModel->where(['business_id'=>$info->id])->get('select_id')->toArray();
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
    function updBusiness(VisaBusinessModel $model,VisaBusinessOptionModel $optionModel,$json){


        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data2['id'] = $this->L->id;
        $data2['update_time'] = TIME_NOW;

        DB::start();
        
        $model->set($data2)->add(true);

        $optionModel->where(['business_id'=>$this->L->id])->remove();

        $obj = json_decode($json);
        !$obj && AJAX::error('json参数格式错误！');
        
        foreach($obj as $k=>$v){

            $data['business_id'] = $this->L->id;
            $data['select_id'] = $k;
            $data['value'] = $v;
            $optionModel->set($data)->add();
        }

        DB::commit();

        $out['type'] = 'business';
        AJAX::success($out);
        

    }
    
    
    /** 我的签证
     * getVisa
     * @return mixed 
     */
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










    # 咨询购买设置
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

    function admin_visa_setting(VisaSelectModel $model,$page = 1,$limit = 10,$type = 0){
        
        $this->L->adminPermissionCheck(77);

        $name = ['','技术移民签证设置','商业签证设置'];
        $name = $name[$type];
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_setting_get?type='.$type,
                'upd'   => '/visa/admin_visa_setting_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_setting_del',

            ];

        # 头部标题设置
        $thead = 
            [

                '排序',
                '表单名',

            ];


        # 列表体设置
        $tbody = 
            [

                'ord',
                'name',

            ];
            

        # 列表内容
        $where = [];
        $where['type'] = $type;

        $list = $model->order('ord')->where($where)->page($page,$limit)->get()->toArray();


        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_setting_get(VisaSelectModel $model,VisaSelectOptionModel $omodel,$id,$type){

        $this->L->adminPermissionCheck(77);

        $name = ['','技术移民签证设置','商业签证设置'];
        $name = $name[$type];

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_setting_get?type='.$type,
                'upd'   => '/visa/admin_visa_setting_upd',
                'back'  => 'visa/setting?type='.$type,
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_setting_del',
                // 'link'  => [
                    
                //     'type'=>'warning',
                //     'name'=>'设置选项',
                //     'href'=>'visa/setting_option?id='.$id,
                    
                // ]

            ];
        $tbody = 
            [

                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'type'  =>  'hidden',
                    'name'  =>  'type',
                    'default'=> $type
                ],
                [
                    'title' =>  '表单名',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '排序正序',
                    'name'  =>  'ord',
                    'size'  =>  '2',
                    'default'=> '0',
                ],
                [
                    'title' =>  '选项',
                    'name'  =>  'option',
                    'type'  =>  'option',
                    'size'  =>  '4',
                    
                ],

            ];

        !$model->field && AJAX::error('字段没有公有化！');

        

        $info = AdminFunc::get($model,$id);
        if(!$info->id)unset($opt['link']);

        $where2['select_id'] = $id;
        $info->option = $omodel->order('id')->where($where2)->get()->toArray();

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_setting_upd(VisaSelectModel $model,VisaSelectOptionModel $omodel,$id,$type){
        $this->L->adminPermissionCheck(77);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        $data['type'] = $type;
        $option = Request::getSingleInstance()->request('option','raw');

        unset($data['id']);
        unset($data['option']);
        $upd = AdminFunc::upd($model,$id,$data);
        $id = $id ? $id : $upd;
        $omodel->where(['select_id'=>$id])->remove();
        
        if($option)foreach($option as $op){

            $omodel->set(['select_id'=>$id,'name'=>$op.''])->add();

        }

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_setting_del(VisaSelectModel $model,VisaSelectOptionModel $omodel,$id){
        $this->L->adminPermissionCheck(77);
        $del = AdminFunc::del($model,$id);
        $omodel->where(['select_id'=>$id])->remove();
        $out['del'] = $del;
        AJAX::success($out);
    }


    
    function admin_visa_setting_option(VisaSelectOptionModel $model,$page = 1,$limit = 999,$id = 0){
        
        $this->L->adminPermissionCheck(77);

        $name = '选项';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_setting_option_get?type='.$id,
                'upd'   => '/visa/admin_visa_setting_option_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_setting_option_del',

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '选项文字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];
        $where['select_id'] = $id;

        $list = $model->order('id')->where($where)->page($page,$limit)->get()->toArray();


        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_setting_option_get(VisaSelectOptionModel $model,$id,$type){

        $this->L->adminPermissionCheck(77);

        $name = '选项';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_setting_option_get?id='.$type,
                'upd'   => '/visa/admin_visa_setting_option_upd',
                'back'  => 'visa/setting_option?id='.$type,
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_setting_option_del',

            ];
        $tbody = 
            [

                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'type'  =>  'hidden',
                    'name'  =>  'select_id',
                    'default'=> $type
                ],
                [
                    'title' =>  '选项文字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],


            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_setting_option_upd(VisaSelectOptionModel $model,$id){
        $this->L->adminPermissionCheck(77);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);

        unset($data['id']);
        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_setting_option_del(VisaSelectOptionModel $model,$id){
        $this->L->adminPermissionCheck(77);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




    function admin_visa_work(VisaWorkModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_work_get',
                'upd'   => '/visa/admin_visa_work_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_work_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_work_get(VisaWorkModel $model,$id){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_work_get',
                'upd'   => '/visa/admin_visa_work_upd',
                'back'  => 'visa/work',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_work_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '年龄',
                    'name'  =>  'age',
                    'size'  =>  '1',
                ],
                [
                    'title' =>  '学历',
                    'name'  =>  'education',
                    'size'  =>  '2',
                ],
                [
                    'title' =>  '专业',
                    'name'  =>  'profession',
                    'size'  =>  '2',
                ],
                [
                    'title' =>  '英语成绩',
                    'name'  =>  'english_results',
                    'size'  =>  '1',
                ],
                [
                    'title' =>  '是否有社保',
                    'name'  =>  'social',
                    'type'  =>  'select',
                    'option'=>[
                        '1'=>'是',
                        '0'=>'否',
                    ]
                ],
                [
                    'title' =>  '签证历史',
                    'name'  =>  'history',
                    'type'  =>  'textarea',
                    
                ],
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_work_upd(VisaWorkModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_work_del(VisaWorkModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




    function admin_visa_family(VisaFamilyModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_family_get',
                'upd'   => '/visa/admin_visa_family_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_family_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_family_get(VisaFamilyModel $model,$id){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_family_get',
                'upd'   => '/visa/admin_visa_family_upd',
                'back'  => 'visa/family',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_family_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '担保人',
                    'name'  =>  'bondsman',
                    'size'  =>  '3',
                ],
                [
                    'title' =>  '被担保人',
                    'name'  =>  'vouchee',
                    'size'  =>  '3',
                ],
                [
                    'title' =>  '签证历史',
                    'name'  =>  'history',
                    'type'  =>  'textarea',
                    
                ],
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_family_upd(VisaFamilyModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_family_del(VisaFamilyModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function admin_visa_refuse(VisaRefuseModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_refuse_get',
                'upd'   => '/visa/admin_visa_refuse_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_refuse_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_refuse_get(VisaRefuseModel $model,$id){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_refuse_get',
                'upd'   => '/visa/admin_visa_refuse_upd',
                'back'  => 'visa/refuse',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_refuse_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '拒签签证',
                    'name'  =>  'type',
                    'size'  =>  '3',
                ],
                [
                    'title' =>  '被拒理由',
                    'name'  =>  'reason',
                    'type'  =>  'textarea',
                ],
                [
                    'title' =>  '为什么觉得不合理',
                    'name'  =>  'my',
                    'type'  =>  'textarea',
                    
                ],
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_refuse_upd(VisaRefuseModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_refuse_del(VisaRefuseModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




    function admin_visa_travel(VisaTravelModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_travel_get',
                'upd'   => '/visa/admin_visa_travel_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_travel_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_travel_get(VisaTravelModel $model,$id){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_travel_get',
                'upd'   => '/visa/admin_visa_travel_upd',
                'back'  => 'visa/travel',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_travel_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '年龄',
                    'name'  =>  'age',
                    'size'  =>  '1',
                ],
                [
                    'title' =>  '性别',
                    'name'  =>  'sex',
                    'size'  =>  '1',
                ],
                [
                    'title' =>  '学历',
                    'name'  =>  'education',
                    'size'  =>  '3',
                ],
                [
                    'title' =>  '存款',
                    'name'  =>  'deposit',
                    'size'  =>  '3',
                ],
                [
                    'title' =>  '房产',
                    'name'  =>  'house',
                    'size'  =>  '3',
                ],
                [
                    'title' =>  '户口所在地',
                    'name'  =>  'residence',
                    'size'  =>  '3',
                ],
                [
                    'title' =>  '读书/在职情况',
                    'name'  =>  'study',
                    'size'  =>  '3',
                    
                ],
                [
                    'title' =>  '签证历史',
                    'name'  =>  'history',
                    'type'  =>  'textarea',
                    
                ],
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_travel_upd(VisaTravelModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_travel_del(VisaTravelModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }



    function admin_visa_marry(VisaMarryModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_marry_get',
                'upd'   => '/visa/admin_visa_marry_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_marry_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_marry_get(VisaMarryModel $model,$id){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_marry_get',
                'upd'   => '/visa/admin_visa_marry_upd',
                'back'  => 'visa/marry',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_marry_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '双方婚史',
                    'name'  =>  'marry',
                    'type'  =>  'textarea',
                ],
                [
                    'title' =>  '共同财产',
                    'name'  =>  'money',
                    'type'  =>  'textarea',
                ],
                [
                    'title' =>  '爱情故事',
                    'name'  =>  'story',
                    'type'  =>  'textarea',
                ],
                [
                    'title' =>  '子女情况',
                    'name'  =>  'child',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '证明材料',
                    'name'  =>  'pic',
                    'type'  =>  'pics',
                ],
                [
                    'title' =>  '结婚日期',
                    'name'  =>  'date',
                    'size'  =>  '3',
                ],
                [
                    'title' =>  '签证历史',
                    'name'  =>  'history',
                    'type'  =>  'textarea',
                    
                ],
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_marry_upd(VisaMarryModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_marry_del(VisaMarryModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }





    function admin_visa_graduate(VisaGraduateModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_graduate_get',
                'upd'   => '/visa/admin_visa_graduate_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_graduate_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_graduate_get(VisaGraduateModel $model,$id){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_graduate_get',
                'upd'   => '/visa/admin_visa_graduate_upd',
                'back'  => 'visa/graduate',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_graduate_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '毕业学校',
                    'name'  =>  'school',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '专业',
                    'name'  =>  'profession',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '毕业时间',
                    'name'  =>  'date',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '英语成绩',
                    'name'  =>  'result',
                    'size'  =>  '2',
                ],
                [
                    'title' =>  '是否有评估',
                    'name'  =>  'assessment',
                    'type'  =>  'select',
                    'option'=>[
                        '1'=>'是',
                        '0'=>'否',
                    ]
                    
                ],
                [
                    'title' =>  '签证历史',
                    'name'  =>  'history',
                    'type'  =>  'textarea',
                    
                ],
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_graduate_upd(VisaGraduateModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_graduate_del(VisaGraduateModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




    function admin_visa_student(VisaStudentModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_student_get',
                'upd'   => '/visa/admin_visa_student_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_student_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_student_get(VisaStudentModel $model,$id){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_student_get',
                'upd'   => '/visa/admin_visa_student_upd',
                'back'  => 'visa/student',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_student_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '申请大学',
                    'name'  =>  'school',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '目标大学',
                    'name'  =>  'target',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '教育背景',
                    'name'  =>  'background',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '英语成绩',
                    'name'  =>  'result',
                    'size'  =>  '2',
                ],
                [
                    'title' =>  '就读专业',
                    'name'  =>  'profession',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '是否父母陪同',
                    'name'  =>  'parent',
                    'type'  =>  'select',
                    'option'=>[
                        '1'=>'是',
                        '0'=>'否',
                    ]
                    
                ],
                [
                    'title' =>  '签证历史',
                    'name'  =>  'history',
                    'type'  =>  'textarea',
                ],
                [
                    'title' =>  '工作经历',
                    'name'  =>  'work',
                    'type'  =>  'textarea',
                ],
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_student_upd(VisaStudentModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_student_del(VisaStudentModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }


    function admin_visa_perpetual(VisaPerpetualModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_perpetual_get',
                'upd'   => '/visa/admin_visa_perpetual_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_perpetual_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_perpetual_get(VisaPerpetualModel $model,$id){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_perpetual_get',
                'upd'   => '/visa/admin_visa_perpetual_upd',
                'back'  => 'visa/perpetual',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_perpetual_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '绿卡获取时间',
                    'name'  =>  'date',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '在线多久',
                    'name'  =>  'time',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '关系',
                    'name'  =>  'relation',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_perpetual_upd(VisaPerpetualModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_perpetual_del(VisaPerpetualModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }




    function admin_visa_technology(VisaTechnologyModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_technology_get',
                'upd'   => '/visa/admin_visa_technology_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_technology_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_technology_get(
        VisaTechnologyModel $model,
        VisaTechnologyOptionModel $optionModel,
        VisaSelectModel $visaSelectModel,$id
        ){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_technology_get',
                // 'upd'   => '/visa/admin_visa_technology_upd',
                'back'  => 'visa/technology',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_technology_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');

        $info = $model->find($id);

        if($info){

            $option = $optionModel->where(['technology_id'=>$info->id])->get('select_id')->toArray();
        }

        $select = $visaSelectModel->select([
            'id,name,GROUP_CONCAT(%F) AS `option`','option.name'
        ],'RAW')->where(['type'=>1])->group('id')->order('ord')->get()->toArray();

        

        foreach($select as &$v){
            $v->option = explode(',',$v->option);
            $options = [];
            foreach($v->option as $o){
                $options[$o] = $o;
            }
            $v->option = $options;
            $v->title = $v->name;
            $v->type="select";
            if($info){
                $v->default = $option[$v->id]->value;
                if(!$v->default)$v->default = '';
            }else{
                $v->default = '';
            }
            array_push($tbody,$v);
        }

        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_technology_upd(VisaTechnologyModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_technology_del(VisaTechnologyModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }



    function admin_visa_business(VisaBusinessModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(78);

        $name = '签证';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_business_get',
                'upd'   => '/visa/admin_visa_business_upd',
                'view'  => 'home/upd',
                'del'   => '/visa/admin_visa_business_del',
                

            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',

            ];


        # 列表体设置
        $tbody = 
            [

                'id',
                'name',

            ];
            

        # 列表内容
        $where = [];


        $list = $model->select('*','user.name')->where($where)->page($page,$limit)->get()->toArray();

        

        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_visa_business_get(
        VisaBusinessModel $model,
        VisaBusinessOptionModel $optionModel,
        VisaSelectModel $visaSelectModel,$id
        ){

        $this->L->adminPermissionCheck(78);

        $name = '查看签证';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/visa/admin_visa_business_get',
                // 'upd'   => '/visa/admin_visa_business_upd',
                'back'  => 'visa/business',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/visa/admin_visa_business_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                
                [
                    'title' =>  '报价',
                    'name'  =>  'price',
                    'size'  =>  '4',
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');

        $info = $model->find($id);

        if($info){

            $option = $optionModel->where(['business_id'=>$info->id])->get('select_id')->toArray();
        }

        $select = $visaSelectModel->select([
            'id,name,GROUP_CONCAT(%F) AS `option`','option.name'
        ],'RAW')->where(['type'=>2])->group('id')->order('ord')->get()->toArray();

        

        foreach($select as &$v){
            $v->option = explode(',',$v->option);
            $options = [];
            foreach($v->option as $o){
                $options[$o] = $o;
            }
            $v->option = $options;
            $v->title = $v->name;
            $v->type="select";
            if($info){
                $v->default = $option[$v->id]->value;
                if(!$v->default)$v->default = '';
            }else{
                $v->default = '';
            }
            array_push($tbody,$v);
        }

        $info = AdminFunc::get($model,$id);

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_visa_business_upd(VisaBusinessModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(78);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        unset($data['id']);

        

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_visa_business_del(VisaBusinessModel $model,$id){
        $this->L->adminPermissionCheck(78);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

}