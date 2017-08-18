<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use stdClass;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;
use DB;
# Model
use App\Lawyer\Model\LawyerModel;
use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\UserModel;
use App\Lawyer\Model\ConsultModel;
use App\Lawyer\Model\FastQuestionModel;
use App\Lawyer\Model\VisaSendModel;
use App\Lawyer\Model\UserAccountModel;
use App\Lawyer\Model\UserMasterPerson;
use App\Lawyer\Model\UserMasterCompany;
use Model;


class MasterController extends Controller{


    function __construct(){
        
        $this->L = L::getSingleInstance();
        
    }

    /** 1.获取下一级平台大使的用户
     * getLowerLevelList
     * @param mixed $model 
     * @return mixed 
     */
    function getLowerLevelList(UserModel $model){

        !$this->L->id && AJAX::error('未登录');
        
        !in_array($this->L->userInfo->master_type ,[0,1]) && AJAX::error('非0级1级平台大使，没有下一级！');

        $list = $model->select('id','avatar','name','phone','create_time')->where(['parent_id'=>$this->L->id])->order('create_time desc')->get()->toArray();
        
        $out['list'] = $list;

        AJAX::success($out);

    }


    /** 2.删除下一级
     * delLowerLevel
     * @param mixed $id 
     * @return mixed 
     */
    function delLowerLevel($id = 0){

        !$this->L->id && AJAX::error('未登录');
        
        !in_array($this->L->userInfo->master_type ,[0,1]) && AJAX::error('非0级1级平台大使，没有下一级！');

        $info = $model->find($id);

        if($info){

            $info->parent_id != $this->L->id && AJAX::error('该用户不是您的下一级!');

            $info->parent_id = 0;
            $info->save();

        }
        

        AJXA::success();

    }


    /** 3.获取客户
     * getCustomerList
     * @param mixed $model 
     * @return mixed 
     */
    function getCustomerList(UserModel $model){
        
        !$this->L->id && AJAX::error('未登录');
        
        !in_array($this->L->userInfo->master_type ,[0,1,2]) && AJAX::error('非平台大使，没有客户！');

        $list = $model->select('id','avatar','name','phone','create_time')->where(['master_id'=>$this->L->id])->order('create_time desc')->get()->toArray();
        
        $out['list'] = $list;

        AJXA::success($out);
        
    }
    

    /** 4.删除客户
     * delCustomer
     * @param mixed $id 
     * @return mixed 
     */
    function delCustomer($id = 0){

        !$this->L->id && AJAX::error('未登录');
        
        !in_array($this->L->userInfo->master_type ,[0,1,2]) && AJAX::error('非平台大使，没有客户！');

        $info = $model->find($id);

        if($info){

            $info->master_id != $this->L->id && AJAX::error('该用户不是您的客户!');

            $info->master_id = 0;
            $info->save();

        }
        

        AJXA::success();

    }


    /** 5.获取我的收款账户
     * getAccount
     * @param mixed $model 
     * @return mixed 
     */
    function getAccount(UserAccountModel $model){

        !$this->L->id && AJAX::error('未登录');

        $list = AdminFunc::get($model,$this->L->id);

        $out['list'] = $list;
        AJAX::success($out);
    }


    /** 6.更新我的收款账户
     * updAccount
     * @param mixed $model 
     * @return mixed 
     */
    function updAccount(UserAccountModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);
        AJAX::success();

    }

    

    /** 7.检查用户是否是01级平台大使
     * checkParent
     * @param mixed $name 
     * @param mixed $phone 
     * @param mixed $model 
     * @return mixed 
     */
    function checkParent($name,$phone,UserModel $model){
        
        $parent = $model->where(['phone'=>$phone])->find();

        !$parent && AJAX::error('用户不存在！');

        $parent->name != $name && AJAX::error('没有该平台大使！');

        !in_array($parent->master_type ,[0,1]) && AJAX::error('非0级1级平台大使，无法成为下一级！');

        AJAX::success();


    }



    /** 8.个人平台大使审核申请
     * applyPerson
     * @param mixed $model 
     * @param mixed $umodel 
     * @param mixed $parent_id 
     * @return mixed 
     */
    function applyPerson(UserMasterPerson $model,UserModel $umodel,$parent_id = 0){

        $parent = $umodel->find($parent_id);
        !$parent && AJAX::error('用户不存在！');
        !in_array($parent->master_type ,[0,1]) && AJAX::error('非0级1级平台大使，无法成为下一级！');

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $parent_id == $this->L->id && AJAX::error('无法成为自己的下一级！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);

        $this->L->userInfo->master_type = 3;
        $this->L->userInfo->save();

        AJAX::success();

    }


    /** 9.获取个人平台大使信息
     * getApplyPerson
     * @param mixed $model 
     * @return mixed 
     */
    function getApplyPerson(UserMasterPerson $model){

        !$this->L->id && AJAX::error('未登录');

        $list = AdminFunc::get($model,$this->L->id);

        $out['list'] = $list;
        AJAX::success($out);
    }


    /** 10.公司平台大使审核申请
     * applyCompany
     * @param mixed $model 
     * @param mixed $umodel 
     * @param mixed $parent_id 
     * @return mixed 
     */
    function applyCompany(UserMasterCompany $model,UserModel $umodel,$parent_id = 0){

        $parent = $umodel->find($parent_id);
        !$parent && AJAX::error('用户不存在！');
        !in_array($parent->master_type ,[0,1]) && AJAX::error('非0级1级平台大使，无法成为下一级！');

        !$this->L->id && AJAX::error('未登录');
        !$model->field && AJAX::error('字段没有公有化！');

        $parent_id == $this->L->id && AJAX::error('无法成为自己的下一级！');

        $data = Request::getSingleInstance()->request($model->field);
        $data['id'] = $this->L->id;
        $data['update_time'] = TIME_NOW;
        
        $model->set($data)->add(true);

        $this->L->userInfo->master_type = 3;
        $this->L->userInfo->master_company = 1;
        $this->L->userInfo->save();

        AJAX::success();

    }


    /** 11.获取公司平台大使信息
     * getapplyCompany
     * @param mixed $model 
     * @return mixed 
     */
    function getapplyCompany(UserMasterCompany $model){

        !$this->L->id && AJAX::error('未登录');

        $list = AdminFunc::get($model,$this->L->id);

        $out['list'] = $list;
        AJAX::success($out);
    }


    /** 12.获取我的平台大使状态
     * getApply
     * @return mixed 
     */
    function getApply(){

        !$this->L->id && AJAX::error('未登录');

        $out['type'] = $this->L->userInfo->master_type;
        $out['company'] = $this->L->userInfo->master_company;

        AJAX::success($out);
    }
    

}