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
use App\Lawyer\Model\ConsultModel;



class LawyerController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();

    }

    /** 获取律师列表
     * getLawyerList
     * @param mixed $type 律师类型
     * @param mixed $page
     * @param mixed $limit
     * @return mixed 
     */
    function getLawyerList($type = 0 ,$pgae = 1 ,$limit = 10,LawyerModel $model){

        $where['type'] = $type;
        $where['active'] = 1;
        $list = $model->select('avatar','name','description','company','site','fee_star','average_reply','feedback_star','type','oneline_time')->where($where)->page($page,$limit)->order('top desc','id desc')->get()->toArray();
        $out['list'] = $list;
        AJAX::success($out);

    }

    /** 获取律师详情
     * getLawyerInfo
     * @param mixed $id 律师的ID
     * @param mixed $model 
     * @return mixed 
     */
    function getLawyerInfo($id = 0,LawyerModel $model){

        $where['id'] = $id;
        $where['active'] = 1;
        $info = $model->select('avatar','name','description','company','site','fee_star','average_reply','feedback_star','type','oneline_time')->where($where)->find();
        !$info && AJAX::error('律师不存在！');
        $out['info'] = $info;
        AJAX::success($out);

    }

    /** 检查律师
     * checkLawyerAuth
     * @param mixed $id 
     * @param mixed $model 
     * @param mixed $limitModel 
     * @param mixed $ajax 
     * @return mixed 
     */
    function checkLawyerAuth($id,$ajax = true){

        $model = LawyerModel::copyMutiInstance();
        $limitModel = UserConsultLimitModel::copyMutiInstance();
        $consultModel = ConsultModel ::copyMutiInstance();

        !$this->L->id && AJAX::error('请登录后重试！');

        $where['id'] = $id;
        $where['active'] = 1;
        $info = $model->where($where)->find();
        !$info && AJAX::error('律师不存在！');

        $type = $info->type;

        $where = ['user_id'=>$this->L->id];
        $where['rule.type'] = $type;

        $auth = $limitModel->select('*','rule.hours')->where($where)->find();

        !$auth && AJAX::error('请开通会员！');

        $auth->death_time < TIME_NOW && AJAX::error('会员已到期，请重新开通会员！');
        $auth->word_count == 0 && AJAX::error('总字数已用完，请重新开通会员！');
        $auth->question_count == 0 && AJAX::error('问题总数已用完，请重新开通会员！');

        $lawyer_id = $this->L->userInfo->lawyer_id;
        if($lawyer_id && $lawyer_id != $id){
            if(!$consultModel->where(['user_id'=>$this->L->id,'lawyer_id'=>$lawyer_id,'which'=>1])->find()){
                $consult = $consultModel->where(['user_id'=>$this->L->id,'lawyer_id'=>$lawyer_id,'which'=>0])->order('create_time')->find();
                if($consult){
                    $consult->create_time + 3600 * $auth->hours > TIME_NOW && AJAX::error('律师已绑定，'.$auth->hours.'小时后未回复可以更换律师！');
                }else{
                    AJAX::error('律师已绑定，请咨询您绑定的律师！');
                }
            }
        }


        $ajax && AJAX::success();

        if(!$ajax){
            unset($auth->hours);
            $obj = new stdClass;
            $obj->auth = $auth;

            return $obj;
        }
        
    }


    /** 发送问题
     * sendQuestionToLawyer
     * @param mixed $id 
     * @param mixed $message 
     * @param mixed $lawyerModel 
     * @param mixed $consultModel 
     * @return mixed 
     */
    function sendQuestionToLawyer($id,$message,LawyerModel $lawyerModel,ConsultModel $consultModel){
        
        !$this->L->id && AJAX::error('请登录！');

        $mee = $this->checkLawyerAuth($id,false);

        $this->L->userInfo->lawyer_id = $id;
        $this->L->userInfo->save();

        $word_count = mb_strlen($message);

        $data['user_id'] = $this->L->id;
        $data['lawyer_id'] = $id;
        $data['which'] = 0;
        $data['create_time'] = TIME_NOW;
        $data['content'] = $message;
        $data['word_count'] = $word_count;

        DB::start();

        $mee->auth->word_count != -1 && $mee->auth->word_count < $word_count && AJAX::error('提问失败，剩余字数不足！');

        $mee->auth->word_count != -1 && $mee->auth->word_count -= $word_count;
        $mee->auth->question_count != -1 && $mee->auth->question_count -= 1;
        $mee->auth->save();
        

        $consultModel->set($data)->add();
  

        DB::commit();

        AJAX::success();

    }


}