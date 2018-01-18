<?php

namespace App\Car\Controller;

use Controller;
use DB;
use stdClass;
use Response;
use Request;
use App\Car\Middleware\L;
use App\Car\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;
use App\Car\Tool\AdminFunc;

# 数据模型
use App\Car\Model\UserModel;
use App\Car\Model\DriverOnlineModel;
use App\Car\Model\MessageModel;
use App\Car\Model\TripModel;
use App\Car\Model\OrderDrivingModel;
use App\Car\Model\OrderTaxiModel;
use App\Car\Model\LocationModel;
use App\Car\Model\UserApplyModel; 
use App\Car\Model\OrderWayModel; 
use App\Car\Model\DriverModel; 
use App\Car\Model\JudgeModel; 
use App\Car\Model\DriverWayModel; 
use App\Car\Model\TagModel; 
use App\Car\Model\PaymentModel; 
use App\Car\Model\AreaModel; 
use App\Car\Model\UserScoreLogModel; 
use App\Car\Model\UserMoneyLogModel; 
use App\Car\Model\UserBankModel; 
use App\Car\Model\UserCouponModel; 
use App\Car\Model\IncomeModel; 
use App\Car\Model\FeedbackModel; 
use App\Car\Model\UsedCarModel; 
use App\Car\Model\RoadModel; 
use App\Car\Model\UserOnlineModel; 
use App\Car\Model\JudgeDriverModel; 
use Model; 

# Traits
use App\Car\Traits\User\OrderTraits;


class UserController extends Controller{

    use OrderTraits;

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
    function login($phone = null,$password =null,UserModel $model,$cookie = null){


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
        
        $user_token = $this->encrypt_token($info);
        $this->cookie && Response::getSingleInstance()->cookie('user_token',$user_token,0);

        $info->last_login = TIME_NOW;
        $info->save();

        $where['user_id'] = $this->L->id;
        $where['isread'] = 0;
        // $e = MessageModel::copyMutiInstance()->where($where)->find();

        // if($e)$data['hasMessage'] = '1';
        // else $data['hasMessage'] = '0';

        $out = [
            'user_token'=>$user_token,
            'id'=>$info->id,
            'avatar'=>$info->avatar,
            'name'=>$info->name,
            // 'type'=>$info->type,
            // 'hasMessage'=>$data['hasMessage']
            
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
    function register($terminal = 0,UserModel $model,$password = null,$phone = null,$phone_captcha,$cookie = false){
        
        
        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        Func::check_phone($phone);
        Func::check_password($password);
        Func::check_phone_captcha($phone,$phone_captcha);

        $info = new stdClass;

        if($parent_id){
            $parent = $model->find($parent_id);
            !$parent && AJAX::error('推荐人不存在！');
            $info->parent_id = $parent_id;
        }
        $info->phone        = $phone;
        $info->terminal     = floor($terminal);
        $info->salt         = Func::randWord(6);
        $info->password     = $this->encrypt_password($password,$info->salt);
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

    function change_password($new_password,$old_password,$cookie = false){

        !$this->L->id && AJAX::error('未登录');

        $model = UserModel::copyMutiInstance();
        
        $userInfo = $this->L->userInfo;

        $password = $this->encrypt_password($old_password,$userInfo->salt);

        if($password != $userInfo->password){
            AJAX::error('原密码错误');
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
    function getMyInfo(UserModel $userModel){

        !$this->L->id && AJAX::error('未登录');

        $info['avatar'] = $this->L->userInfo->avatar;
        $info['name'] = $this->L->userInfo->name;
        $info['sex'] = $this->L->userInfo->sex;
        $info['phone'] = $this->L->userInfo->phone;
        $info['id'] = $this->L->userInfo->id;
        $info['car_number_1'] = $this->L->userInfo->car_number_1;
        $info['car_number_2'] = $this->L->userInfo->car_number_2;
        $info['car_number_3'] = $this->L->userInfo->car_number_3;
        $info['birth'] = $this->L->userInfo->birth;
        $info['count'] = '999';

        $out['info'] = $info;

        AJAX::success($out);
    }

    # 修改我的信息
    function changeMyInfo($name,$avatar,$sex,$car_number_1,$car_number_2,$car_number_3,$birth){

        !$this->L->id && AJAX::error('未登录');

        $name && $this->L->userInfo->name = $name;
        $avatar && $this->L->userInfo->avatar = $avatar;
        $car_number_1 && $this->L->userInfo->car_number_1 = $car_number_1;
        $car_number_2 && $this->L->userInfo->car_number_2 = $car_number_2;
        $car_number_3 && $this->L->userInfo->car_number_3 = $car_number_3;
        $birth && $this->L->userInfo->birth = $birth;
        $sex != NULL && $this->L->userInfo->sex = $sex;

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

        $model->set(['user_id'=>$this->L->id,'content'=>$content,'create_time'=>TIME_NOW])->add();

        AJAX::success();

    }

    /** 获取我的车辆信息
     * getMyCarList
     * @return mixed 
     */
    function getMyCarList(){

        !$this->L->id && AJAX::error('未登录');

        if($this->L->userInfo->car_number_1)$list[] = $this->L->userInfo->car_number_1;
        if($this->L->userInfo->car_number_2)$list[] = $this->L->userInfo->car_number_2;
        if($this->L->userInfo->car_number_3)$list[] = $this->L->userInfo->car_number_3;

        $out['list'] = $list;
        $out['count'] = count($list);
        AJAX::success($out);
        
    }


    /** 删除我的车
     * delMyCar
     * @param mixed $id 
     * @return mixed 
     */
    function delMyCar($id){

        !$this->L->id && AJAX::error('未登录');

        if($this->L->userInfo->car_number_1)$list[] = $this->L->userInfo->car_number_1;
        if($this->L->userInfo->car_number_2)$list[] = $this->L->userInfo->car_number_2;
        if($this->L->userInfo->car_number_3)$list[] = $this->L->userInfo->car_number_3;

        unset($list[$id - 1]);

        $list = array_values($list);

        foreach($list as $k=>$v){
            $n = 'car_number_'.($k+1);
            $this->L->userInfo->$n = $v;
        }

        $this->L->userInfo->save();

        $out['count'] = count($list);
        $out['list'] = $list;

        AJAX::success($out);
    }

    /** 删除我的车
     * delMyCar
     * @param mixed $id 
     * @return mixed 
     */
    function addMyCar($car_number){

        !$this->L->id && AJAX::error('未登录');

        if($this->L->userInfo->car_number_1)$list[] = $this->L->userInfo->car_number_1;
        if($this->L->userInfo->car_number_2)$list[] = $this->L->userInfo->car_number_2;
        if($this->L->userInfo->car_number_3)$list[] = $this->L->userInfo->car_number_3;

        if(count($list) == 3){
            AJAX::error('无法添加更多的车');
        }
        if(!$car_number)AJAX::error('车牌号错误');

        $list[] = $car_number;

        $list = array_values($list);

        foreach($list as $k=>$v){
            $n = 'car_number_'.($k+1);
            $this->L->userInfo->$n = $v;
        }

        $this->L->userInfo->save();

        $out['count'] = count($list);
        $out['list'] = $list;

        AJAX::success($out);
    }
    
    

    

    function hasMessage(MessageModel $model){

        !$this->L->id && AJAX::error('未登录');

        $where['user_id'] = $this->L->id;
        $where['isread'] = 0;
        $e = $model->where($where)->find();

        if($e)$data['hasMessage'] = '1';
        else $data['hasMessage'] = '0';

        AJAX::success($data);

    }



    
}