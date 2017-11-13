<?php

namespace App\App\Controller;

use Controller;
use DB;
use stdClass;
use Response;
use Request;
use App\App\Middleware\L;
use App\App\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\App\Model\UserModel;
use App\App\Model\MessageModel;
use App\App\Model\UserDateModel;
use App\App\Model\DoctorModel;
use Model; 


class UserController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();
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
        $model = UserModel::copyMutiInstance();
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
        Response::getSingleInstance()->cookie('user_token','',-3600);
        AJAX::success();
    }

    /** 登录
     * 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $cookie 
     * @return mixed 
     */
    function login($type = 0,$code = '',$phone = null,$password =null,UserModel $model,$cookie = null){

        if($type == 0){
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

            
        }
        if($type == 1){
            
            !$code && AJAX::error('无法识别第三方标示！');
            $info = $model->where(['qq'=>$code])->find();
        }elseif($type == 2){

            !$code && AJAX::error('无法识别第三方标示！');
            $info = $model->where(['wx'=>$code])->find();
        }
        !$info && AJAX::error('用户不存在',401);
        !$info->active && AJAX::error('账号已被禁用，请联系管理员！');
        
        //输出登录返回信息
        $this->_out_info($info);


    }


    /** 登录绑定
     * bind
     * @param mixed $type 
     * @param mixed $code 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $model 
     * @param mixed $cookie 
     * @return mixed 
     */
    function bind($type = 0,$code = '',$phone = null,$password =null,UserModel $model,$cookie = null){

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

        if($type == 1){
            
            !$code && AJAX::error('无法识别第三方标示！');
            $info->qq = $code;
        }elseif($type == 2){

            !$code && AJAX::error('无法识别第三方标示！');
            $info->wx = $code;
        }

        !$info->active && AJAX::error('账号已被禁用，请联系管理员！');

        $info-save();
        
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
        $this->cookie && Response::getSingleInstance()->cookie('user_token',$user_token,0);

        $out = [
            'user_token'=>$user_token,
            'id'=>$info->id,
            'avatar'=>$info->avatar,
            'name'=>$info->name,
            'type'=>$info->type,
            
        ];
        
        AJAX::success($out);
    }


    /** 注册
     * register
     * @param mixed $password 
     * @param mixed $phone 
     * @param mixed $phone_captcha 
     * @param mixed $cookie 
     * @return mixed 
     */
    function register($type = 0,$code = '',$terminal = 0,UserModel $model,$password = null,$phone = null,$phone_captcha,$cookie = false,$parent_id = 0){
        
        
        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        Func::check_phone($phone);
        Func::check_password($password);
        Func::check_phone_captcha($phone,$phone_captcha);

        $info = new stdClass;

        
        $info->phone        = $phone;
        $info->terminal     = floor($terminal);
        $info->salt         = Func::randWord(6);
        $info->password     = $this->encrypt_password($password,$info->salt);

        if($type == 1){
            
            !$code && AJAX::error('无法识别第三方标示！');
            $model->where(['qq'=>$code])->find() && AJAX::error('已绑定账号，请勿重复绑定！');
            $info->qq = $code;
        }elseif($type == 2){

            !$code && AJAX::error('无法识别第三方标示！');
            $model->where(['wx'=>$code])->find() && AJAX::error('已绑定账号，请勿重复绑定！');
            $info->wx = $code;
        }

        $this->_add_user($info);
    }

    



    /** 修改密码
     * forget_password
     * @param mixed $new_password 
     * @param mixed $phone 
     * @param mixed $phone_captcha 
     * @param mixed $cookie 
     * @return mixed 
     */
    function forget_password($new_password,$phone,$phone_captcha,$cookie = false){

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        Func::check_phone_captcha($phone,$phone_captcha);

        $model = UserModel::copyMutiInstance();
        if(!$userInfo = $model->where(['phone'=>$phone])->find()){

            AJAX::error('用户不存在！');
        }

        $userInfo->password = $this->encrypt_password($new_password,$userInfo->salt);
        $userInfo->save();

        $this->_out_info($userInfo);


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
    function getMyInfo(UserDateModel $userDateModel){

        !$this->L->id && AJAX::error('未登录');

        $info = $this->L->userInfo;

        unset($info->password);
        unset($info->salt);
        unset($info->qq);
        unset($info->wx);

        $list = $userDateModel->select('*','date.start_time','date.end_time','doctor.name>doctor_name','clinic.name>clinic_name')->where(['user_id'=>$this->L->id])->where('status>2')->order('create_time desc')->get()->toArray();

        $year = [];

        foreach($list as $v){
            $year[$v->year][] = $v;
        }
        $year2 = [];
        foreach($year as $k=>$v){
            $year2[] = [
                'key'=>$k,
                'value'=>$v
            ];
        }


        $out['info'] = $info;
        $out['year'] = $year2;

        AJAX::success($out);
    }

    # 修改我的信息
    function changeMyInfo($name,$avatar,$age){

        !$this->L->id && AJAX::error('未登录');

        $name && $this->L->userInfo->name = $name;
        $avatar && $this->L->userInfo->avatar = $avatar;
        $age && $this->L->userInfo->age = $age;

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
    function feedback($content,UserFeedbackModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$content && AJAX::error('内容不能为空！');

        $model->set(['user_id'=>$this->L->id,'content'=>$content,'create_time'=>TIME_NOW])->add();

        AJAX::success();

    }


    /** 预约
     * date_15
     * @param mixed $clinic_id 
     * @param mixed $doctor_id 
     * @param mixed $userDateModel 
     * @param mixed $doctorModel 
     * @return mixed 
     */
    function date($clinic_id,$doctor_id,UserDateModel $userDateModel,DoctorModel $doctorModel){

        !$this->L->id && AJAX::error('未登录');

        $doctor = $doctorModel->find($doctor_id);

        !$doctor && AJAX::error('医生不存在！');
        $data['clinic_id'] = $clinic_id;
        $data['doctor_id'] = $doctor->id;
        $data['user_id'] = $this->L->id;

        $userDateModel->where($data)->where('status<2')->find() && AJAX::error('您已经预约过了，不能重复预约！');

        $data['create_time']  = TIME_NOW;
        $data['year'] = date('Y',TIME_NOW);
        $data['month'] = date('m',TIME_NOW);
        $data['day'] = date('d',TIME_NOW);

        $data['status'] = $doctor->status ? '1' : '0';

        $userDateModel->set($data)->add();

        AJAX::success();

    }

    /** 我的预约
     * myDate
     * @param mixed $userDateModel 
     * @param mixed $page=1 
     * @param mixed $limit=10 
     * @param mixed $type 
     * @return mixed 
     */
    function myDate(UserDateModel $userDateModel,$page=1,$limit=10,$type = 1){
        
        !$this->L->id && AJAX::error('未登录');

        $where['user_id'] = $this->L->id;

        if($type == 1){
            $where['z1'] = ['status BETWEEN 0 AND 2'];
        }elseif($type == 2){
            $where['status'] = 3;
        }elseif($type == 3){
            $where['status'] = 4;
        }

        $list = $userDateModel->select('*','date.start_time','date.end_time','doctor.name','doctor.avatar','doctor.experience','doctor.skill','doctor.status>doctor_status')->where($where)->page($page,$limit)->get()->toArray();

        // echo $userDateModel->sql;die();
        $out['list'] = $list;
        AJAX::success($out);

    }

    /** 评价
     * judge
     * @param mixed $userDateModel 
     * @param mixed $id 
     * @param mixed $comment 
     * @param mixed $star 
     * @return mixed 
     */
    function judge(UserDateModel $userDateModel,$id,$comment,$star){

        !$this->L->id && AJAX::error('未登录');

        !$comment && AJAX::error('评论不能为空！');
        (!$star || $star<0 || $star > 5) && AJAX::error('评星错误！');

        $info = $userDateModel->find($id);
        $info->status != 3 && AJAX::error('该订单还不能评价！');

        $info->comment = $comment;
        $info->star = $star;
        $info->status = 4;
        $info->save();

        AJAX::success();


    }
}