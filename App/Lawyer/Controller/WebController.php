<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use Response;
use View;
use Request;
use stdClass;
use App\Lawyer\Middleware\L2;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;
use DB;
# Model
use App\Lawyer\Model\LawyerModel;
use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\ConsultModel;
use App\Lawyer\Model\FastQuestionModel;



class WebController extends Controller{


    function __construct(){

        $this->L = L2::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    /** 给密码加密
     * 2333
     * @param mixed $password 
     * @param mixed $salt 
     * @return mixed 
     */
    public function encrypt_password($password,$salt){
        return sha1($this->salt.md5($password).$salt);
    }
    /** 生成登录TOKEN
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function encrypt_token($info){
        return Func::randWord().Func::aes_encode(Func::randWord().base64_encode(sha1($info->password.$this->salt.TIME_NOW).'|'.$info->id.'|'.TIME_NOW));
    }

    /** 登录
     * 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $cookie 
     * @return mixed 
     */
    function login($phone = null,$password =null,LawyerModel $model,$cookie = null){


        //检查参数是否存在
        !$phone && AJAX::error('账号不能为空！');
        !$password && AJAX::error('密码不能为空！');
        
        //找到对应手机号的用户
        $info = $model->where('phone=%n',$phone)->find();
        !$info && AJAX::error('用户不存在');

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        # 验证密码 加密算法采用  sha1(网站干扰码+md5(密码)+用户干扰码)
        $encryptedPassword = $this->encrypt_password($password,'');
        if($encryptedPassword!=$info->password)AJAX::error('密码错误');

        //输出登录返回信息
        $this->_out_info($info);


    }

    /** 输出用户登录信息
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function _out_info($info){
        
        $user_token = $this->encrypt_token($info);
        
        $this->cookie && Response::getSingleInstance()->cookie('lawyer_token',$user_token,0);
        
        $out = [
            'lawyer_token'=>$user_token,
            'id'=>$info->id,
            'avatar'=>$info->avatar,
            'name'=>$info->name
            
        ];
        
        AJAX::success($out);
    }


    /** 获取聊天记录
     * getChatList
     * @param mixed $id 
     * @return mixed 
     */
    function getChatList($id,ConsultModel $consultModel,$page = 1,$limit = 10){

        !$this->L->id && AJAX::error('未登录');
        
        $where['lawyer_id'] = $this->L->id;
        $where['user_id'] = $id;

        $list = $consultModel->select('*','lawyer.avatar>lawyer_avatar','user.avatar>user_avatar')->where($where)->page($page,$limit)->order('create_time desc')->get('create_time')->toArray();
        
        krsort($list);

        $out['list'] = array_values($list);

        AJAX::success($out);

    }
    

    /** 我的咨询列表
     * getChatList
     * @return mixed 
     */
    function getMyChat(ConsultModel $consultModel){

        !$this->L->id && AJAX::error('未登录');
        
        $where['lawyer_id'] = $this->L->id;
        $consultModel->distinct();

        $lawyer_list = $consultModel->where($where)->get_field('user_id')->toArray();
        
        $list = [];
        foreach($lawyer_list as $v){

            $list[] = $consultModel->select('content','create_time','user_id','user.name','user.avatar')->where($where)->where(['user_id'=>$v])->order('create_time desc')->find();
        }

        $out['list'] = $list;

        AJAX::success($out);

    }



    /** 回复问题
     * sendQuestionToUser
     * @param mixed $id 
     * @param mixed $message 
     * @return mixed 
     */
    function sendQuestionToUser($id,$message,ConsultModel $consultModel){
        
        !$this->L->id && AJAX::error('未登录');

        $word_count = mb_strlen($message);

        $data['user_id'] = $id;
        $data['lawyer_id'] = $this->L->id;
        $data['which'] = 1;
        $data['create_time'] = TIME_NOW;
        $data['content'] = $message;
        $data['word_count'] = $word_count;

        DB::start();

        $consultModel->set($data)->add();
  

        DB::commit();

        AJAX::success();

    }

}