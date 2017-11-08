<?php

namespace App\App\Controller;

use Controller;
use DB;
use stdClass;
use Response;
use Request;
use App\App\Middleware\L2;
use App\App\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\App\Model\UserModel;
use App\App\Model\MessageModel;
use App\App\Model\UserDateModel;
use App\App\Model\DateModel;
use App\App\Model\UserTagModel;
use App\App\Model\DoctorModel;
use App\App\Model\TagModel;
use App\App\Model\DoctorFeedbackModel;
use Model; 


class DoctorController extends Controller{


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

    /** 添加新用户
     * _add_doctor
     * @param mixed $info 
     * @return mixed 
     */
    private function _add_doctor($info){

        $info->create_time = TIME_NOW;
        $model = DoctorModel::copyMutiInstance();
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
        Response::getSingleInstance()->cookie('doctor_token','',-3600);
        AJAX::success();
    }

    /** 登录
     * 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $cookie 
     * @return mixed 
     */
    function login($phone = null,$password =null,DoctorModel $model,$cookie = null){


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

        

        $info->save();
        
        //输出登录返回信息
        $this->_out_info($info);


    }

    

    /** 输出用户登录信息
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function _out_info($info){
        
        $doctor_token = $this->encrypt_token($info);
        $this->cookie && Response::getSingleInstance()->cookie('doctor_token',$doctor_token,0);

        $out = [
            'doctor_token'=>$doctor_token,
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
    function register($terminal = 0,DoctorModel $model,$password = null,$phone = null,$phone_captcha,$cookie = false,$parent_id = 0){
        
        
        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        Func::check_phone($phone);
        Func::check_password($password);
        Func::check_phone_captcha($phone,$phone_captcha);

        $info = new stdClass;

        if($id){
            $parent = $model->find($parent_id);
            !$parent && AJAX::error('推荐人不存在！');
            $info->parent_id = $parent_id;
        }
        $info->phone        = $phone;
        $info->terminal     = floor($terminal);
        $info->salt         = Func::randWord(6);
        $info->password     = $this->encrypt_password($password,$info->salt);


        $this->_add_doctor($info);
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

        $model = DoctorModel::copyMutiInstance();
        if(!$doctorInfo = $model->where(['phone'=>$phone])->find()){

            AJAX::error('用户不存在！');
        }

        $doctorInfo->password = $this->encrypt_password($new_password,$doctorInfo->salt);
        $doctorInfo->save();

        $this->_out_info($doctorInfo);


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

        $info = $this->L->doctorInfo;

        unset($info->password);
        unset($info->salt);


        $out['info'] = $info;

        AJAX::success($out);
    }

    # 修改我的信息
    function changeMyInfo($name,$avatar,$age,$introduce,$skill,$resume){

        !$this->L->id && AJAX::error('未登录');

        $name && $this->L->doctorInfo->name = $name;
        $introduce && $this->L->doctorInfo->introduce = $introduce;
        $avatar && $this->L->doctorInfo->avatar = $avatar;
        $age && $this->L->doctorInfo->age = $age;
        $skill && $this->L->doctorInfo->skill = $skill;
        $resume && $this->L->doctorInfo->resume = $resume;

        $this->L->doctorInfo->save();

        AJAX::success();
    }

    # 修改头像
    function changeMyAvatar(){

        !$this->L->id && AJAX::error('未登录');

        $out['path'] = $path = Func::uploadFiles('avatar',100,100);
        !$path && AJAX::error('上传失败，没有找到上传文件！');
        
        $this->L->doctorInfo->avatar = $path;
        $this->L->doctorInfo->save();

        AJAX::success($out);

    }

    

    /** 意见反馈
     * feedback
     * @param mixed $content 反馈内容
     * @return mixed 
     */
    function feedback($content,DoctorFeedbackModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$content && AJAX::error('内容不能为空！');

        $model->set(['doctor_id'=>$this->L->id,'content'=>$content,'create_time'=>TIME_NOW])->add();

        AJAX::success();

    }



    /** 获取用户
     * getUser
     * @param mixed $userDateModel 
     * @param mixed $userTagModel 
     * @param mixed $page=1 
     * @param mixed $limit=10 
     * @param mixed $status 
     * @param mixed $name 
     * @param mixed $year 
     * @param mixed $month 
     * @param mixed $day 
     * @return mixed 
     */
    function getUserList(UserDateModel $userDateModel,UserTagModel $userTagModel,$page=1,$limit=10,$status = 0,$name,$year,$month,$day){
        
        !$this->L->id && AJAX::error('未登录');
        // $this->L->id = 4;
        $where['doctor_id'] = $this->L->id;

        if($status == 3){
            $where['status'] = ['status BETWEEN 3 AND 4'];
        }elseif($status == 2){
            $where['status'] = 2;
        }elseif($status == 1){
            $where['status'] = 1;
        }elseif($status == 0){
            $where['status'] = 0;
        }elseif($status == -1){
            $where['status'] = -1;
        }

        /** 搜索患者名字 */
        if($name){
            $where['s'] = ['user.name LIKE %n','%'.$name.'%'];
        }

        /** 搜索日期 */
        if($year && $month && $day){

            $where['year'] = $year;
            $where['month'] = $month;
            $where['day'] = $day;

        }

        $list = $userDateModel->select('*','date.start_time','date.end_time','user.name','user.avatar','user.phone','user.age')->where($where)->page($page,$limit)->get()->toArray();
        // echo $userDateModel->sql;die();
        foreach($list as &$v){
            $v->tags = [];
            $tags = $userTagModel->select('tag.name')->order('times desc')->get()->toArray();
            foreach($tags as $v2)$v->tags[] = $v2->name;

            $v->tags =  implode(',',$v->tags);

        }

        $out['list'] = $list;
        AJAX::success($out);

    }

    /** 获取预约详情
     * getUserInfo
     * @param mixed $userDateModel 
     * @param mixed $userTagModel 
     * @param mixed $id 
     * @return mixed 
     */
    function getUserInfo(UserDateModel $userDateModel,UserTagModel $userTagModel,$id){

        !$this->L->id && AJAX::error('未登录');
        $info = $userDateModel->select('*','date.start_time','date.end_time','user.name','user.avatar','user.phone','user.age')->find($id);

        !$info && AJAX::error('预约不存在！');
        $info->tags = [];
        $tags = $userTagModel->select('tag.name')->order('times desc')->get()->toArray();
        foreach($tags as $v2)$info->tags[] = $v2->name;

        $out['info'] = $info;
        AJAX::success($out);
    }

    /** 设定时间段
     * setTime
     * @param mixed $user_id 
     * @param mixed $time 
     * @return mixed 
     */
    function setTime($id,$date_id,UserDateModel $userDateModel){

        !$this->L->id && AJAX::error('未登录');

        $date = $userDateModel->find($id);
        !$date && AJAX::error('预约不存在！');
        !$date->doctor != $this->L->id && AJAX::error('无权限操作！');

        $date->date_id = $date_id;
        $date->status = 2;
        $date->save();

        AJAX::success();
    }

    /** 取消预约
     * cancel
     * @param mixed $userDateModel 
     * @param mixed $id 
     * @return mixed 
     */
    function cancel(UserDateModel $userDateModel,$id){

        !$this->L->id && AJAX::error('未登录');
        $userDateModel->set(['status'=>-1])->save($id);
        AJAX::success();
    }
    

    /** 评价
     * judge
     * @param mixed $userDateModel 
     * @param mixed $id 
     * @param mixed $comment 
     * @param mixed $star 
     * @return mixed 
     */
    function judge(UserDateModel $userDateModel,$id,$content,$tooth,$tags,TagModel $tagModel,UserTagModel $userTagModel){

        !$this->L->id && AJAX::error('未登录');

        !$content && AJAX::error('评论不能为空！');

        $info = $userDateModel->find($id);
        $info->status != 2 && AJAX::error('该订单还不能评价！');

        $info->content = $content;
        $info->tooth = $tooth;
        $info->status = 3;
        

        $tags = $tags ? explode(',',$tags) : [];
        if($tags)$tag = $tags->where('id IN (%c)',$tags)->get()->toArray();
        else $tag = [];

        $tag_names = [];

        foreach($tag as $v){
            $tag_names[] = $v->name;
            
            $m = $userTagModel->where(['user_id'=>$info->user_id,'tag_id'=>$v->id])->find();
            if($m){
                $m->times += 1;
                $m->save();
            }else{
                $userTagModel->set(['user_id'=>$info->user_id,'tag_id'=>$v->id])->add();
            }

        }
        $tag_names = implode($tag_names);
        $info->tags = $tag_names;
        
        
        
        $info->save();
        AJAX::success();


    }


    /** 获取预约时间段列表
     * getTime
     * @return mixed 
     */
    function getTimeList(DateModel $model,UserDateModel $userDateModel){

        !$this->L->id && AJAX::error('未登录');

        $list = $model->get()->toArray();

        
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $date_ids = $userDateModel->where(['doctor_id'=>$this->L->id,'year'=>$year,'month'=>$month,'day'=>$day])->get_field('date_id')->toArray();

        foreach($list as &$v){

            $v->disabled = $v->start_time < date('H:i') ? '1' : '0';
            if(in_array($v->id,$date_ids))$v->disabled = '1';
        }

        $out['list'] = $list;
        $out['date_ids'] = $date_ids;
        AJAX::success($out);
    }


    
}