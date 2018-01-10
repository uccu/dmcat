<?php

namespace App\Car\Controller;

use Controller;
use DB;
use stdClass;
use Response;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\Car\Model\AdminModel;
use App\Car\Model\MessageModel;


class AdminController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
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

    /** 添加新用户
     * _add_user
     * @param mixed $info 
     * @return mixed 
     */
    private function _add_user($info){

        $info->create_time = TIME_NOW;
        $model = AdminModel::copyMutiInstance();
        $model->where(['phone'=>$info->phone])->find() && AJAX::error('手机号已存在');
        

        DB::start();


        $info->id = $model->set($info)->add()->getStatus();
        !$info->id && AJAX::error('新用户创建失败');
        
        $info = $model->find($info->id);
        $info->name = '用户'.Func::add_zero($info->id,6);
        $info->avatar = 'noavatar.png';
        $info->save();

        DB::commit();
        
        $this->_out_info($info);
    }

    /** 登出
     * 
     * @return mixed 
     */
    function logout(){
        Response::getSingleInstance()->cookie('admin_token','',-3600);
        header('Location:/admin/login');
    }

    /** 登录
     * 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $cookie 
     * @return mixed 
     */
    function login($phone = null,$password =null,AdminModel $model,$cookie = null){


        //检查参数是否存在
        !$phone && AJAX::error('账号不能为空！');
        !$password && AJAX::error('密码不能为空！');
        
        //找到对应手机号的用户
        $info = $model->where('phone=%n',$phone)->find();
        !$info && AJAX::error('用户不存在');

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        # 验证密码 加密算法采用  sha1(网站干扰码+md5(密码)+用户干扰码)
        $encryptedPassword = $this->encrypt_password($password,$info->salt);
        if($encryptedPassword!=$info->password)AJAX::error('密码错误');

        !$info->active && AJAX::error('账号已被禁用，请联系管理员！');
        
        //输出登录返回信息
        $this->_out_info($info);


    }

    

    /** 输出用户登录信息
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function _out_info($info){
        
        $admin_token = $this->encrypt_token($info);
        $this->cookie && Response::getSingleInstance()->cookie('admin_token',$admin_token,0);

        $out = [
            'admin_token'=>$admin_token,
            'id'=>$info->id,
            'avatar'=>$info->avatar,
            'name'=>$info->name,
            
        ];
        
        AJAX::success($out);
    }

    function change_password($pwd){
        !$this->L->id && AJAX::error('未登录');
        !$this->L->userInfo->type && AJAX::error('嘿嘿嘿');

        $this->L->userInfo->password = $this->encrypt_password($pwd,$this->L->userInfo->salt);
        $this->L->userInfo->save();

        Response::getSingleInstance()->cookie('user_token','',-3600);
        AJAX::success(null,200,'/admin/login');
    }



    

    
    /** 发送手机验证码
     * captcha
     * @param mixed $phone 手机号
     * @param mixed $out 是否输出AJAX
     * @return mixed 
     */
    function captcha($phone,$out = 1) {

        Func::check_phone($phone);
        Func::msm($phone,$type);
        if($out)AJAX::success();

    }



    # 我的信息
    function getMyInfo(){

        !$this->L->id && AJAX::error('未登录');

        $info['avatar'] = $this->L->userInfo->avatar;
        $info['name'] = $this->L->userInfo->name;
        $info['sex'] = $this->L->userInfo->sex;
        $info['phone'] = $this->L->userInfo->phone;
        $info['id'] = $this->L->userInfo->id;

        $out['info'] = $info;

        AJAX::success($out);
    }

    # 修改我的信息
    function changeMyInfo($name,$avatar,$sex){

        !$this->L->id && AJAX::error('未登录');

        $name && $this->L->userInfo->name = $name;
        $avatar && $this->L->userInfo->avatar = $avatar;
        $sex && $this->L->userInfo->sex = $sex;

        $this->L->userInfo->save();

        AJAX::success();
    }

    # 修改头像
    function changeMyAvatar(){

        !$this->L->id && AJAX::error('未登录');

        $out['path'] = $path = Func::uploadFiles('avatar',100,100);
        !$path && AJAX::error('上传失败，没有找到上传文件！');
        
        $this->L->userInfo->avatar = $path;
        $this->L->userInfo->save();

        AJAX::success($out);

    }

    /** 获取我的消息
     * myMessage
     * @param mixed $page 
     * @param mixed $limit 
     * @return mixed 
     */
    function getMyMessage(MessageModel $model,$page = 1,$limit = 10){
        
        !$this->L->id && AJAX::error('未登录');
        $where['user_id'] = $this->L->id;
        $list = $model->where($where)->order('create_time desc')->page($page,$limit)->get()->toArray();

        $model->where($where)->set(['isread'=>1])->save();
        
        $out['list'] = $list;
        AJAX::success($out);
    }

    /** 意见反馈
     * feedback
     * @param mixed $content 反馈内容
     * @return mixed 
     */
    function feedback($content,FeedbackModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$content && AJAX::error('内容不能为空！');

        $model->set(['user_id'=>$this->L->id,'content'=>$content])->add();

        AJAX::success();

    }
    
}