<?php

namespace App\Car\Controller;

use Controller;
use DB;
use stdClass;
use Response;
use Request;
use App\Car\Middleware\L2;
use App\Car\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\Car\Model\DriverModel;
use App\Car\Model\DriverMessageModel;
use App\Car\Model\DriverFeedbackModel;
use App\Car\Model\OrderDrivingModel;
use App\Car\Model\OrderTaxiModel;
use App\Car\Model\TripModel;
use App\Car\Model\DriverFundModel; 
use App\Car\Model\UserModel; 
use App\Car\Model\PaymentModel;
use App\Car\Model\JudgeModel;
use App\Car\Model\DriverIncomeModel;
use App\Car\Model\DriverBankModel;
use App\Car\Model\DriverMoneyLogModel;
use App\Car\Model\AreaModel;
use App\Car\Model\DriverApplyModel;
use App\Car\Model\DriverOnlineModel;

use Model; 

# Traits
use App\Car\Traits\Driver\OrderTraits;


class DriverController extends Controller{

    use OrderTraits;

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
     * _add_user
     * @param mixed $info 
     * @return mixed 
     */
    private function _add_user($info){

        $info->create_time = TIME_NOW;
        $model = DriverModel::copyMutiInstance();
        $model->where(['phone'=>$info->phone])->find() && AJAX::error('手机号已存在');
        

        DB::start();


        $info->id = $model->set($info)->add()->getStatus();
        !$info->id && AJAX::error('新用户创建失败');
        
        $info = $model->find($info->id);
        $info->name = '用户'.Func::add_zero($info->id,6);
        $info->avatar = 'noavatar.png';
        $info->save();

        $parent_id = $info->parent_id;

        for($i=0;$i<3;$i++){

            if($parent_id){

                $parent = $model->find($parent_id);
                if($parent){

                    $parent->fans += 1;
                    $parent->save();
                    $parent_id = $parent->parent_id;
                }else break;

            }else break;

        }
        

        DB::commit();
        
        $this->_out_info($info);
    }

    /** 登出
     * 
     * @return mixed 
     */
    function logout(){
        Response::getSingleInstance()->cookie('driver_token','',-3600);
        AJAX::success();
    }

    /** 登录
     * 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $cookie 
     * @return mixed 
     */
    function login($phone = null,$password =null,DriverModel $model,$cookie = null){


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
        
        $driver_token = $this->encrypt_token($info);
        $this->cookie && Response::getSingleInstance()->cookie('driver_token',$driver_token,0);

        $info->last_login = TIME_NOW;
        $info->save();
        
        $where['driver_id'] = $this->L->id;
        $where['isread'] = 0;
        $e = DriverMessageModel::copyMutiInstance()->where($where)->find();

        if($e)$data['hasMessage'] = '1';
        else $data['hasMessage'] = '0';

        $apply_status = DriverApplyModel::copyMutiInstance()->where(['id'=>$info->id])->order('create_time desc')->find()->status;
        ;

        $out = [
            'driver_token'=>$driver_token,
            'id'=>$info->id,
            'avatar'=>$info->avatar,
            'name'=>$info->name,
            'tpye_driving'=>$info->type_driving,
            'tpye_taxi'=>$info->type_taxi,
            'hasMessage'=>$data['hasMessage'],
            'apply_status'=> NULL === $apply_status ? '-2' : $apply_status
            
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
    function register(DriverModel $model,$password = null,$phone = null,$phone_captcha,$cookie = false,$parent_id = 0,$city_id = 0){
        
        
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
        $info->city_id      = $city_id;
        $info->salt         = Func::randWord(6);
        $info->password     = $this->encrypt_password($password,$info->salt);
        $this->_add_user($info);
    }


    /** 申请成为司机
     * apply
     * @param mixed $driverApplyModel 
     * @param mixed $car_number 
     * @param mixed $brand 
     * @return mixed 
     */
    function apply(DriverApplyModel $driverApplyModel,$driving_permit,$driving_license,$car_number = '',$brand = '',$city_id = 0,$latitude,$longitude,$type=0, AreaModel $areaModel){

        !$this->L->id && AJAX::error('未登录');
        
        if(!$city_id){

            $area = Func::getArea($latitude,$longitude);
            if(!$area)AJAX::error('位置获取失败');
            $city_id = $areaModel->where(['areaName'=>$area->city,'area_t.areaName'=>$area->province])->find()->id;

            if(!$city_id){

                $city_id = $areaModel->where(['areaName'=>$area->district,'area_t.areaName'=>$area->city])->find()->id;

            }

            !$city_id && AJAX::error('区域ID获取失败！');
        }

        $data['id'] = $this->L->id;
        $data['car_number'] = $car_number;
        $data['brand'] = $brand;
        $data['type'] = $type;
        if($driving_permit)$data['driving_permit'] = $driving_permit;
        else $data['driving_permit'] = Func::uploadFiles('driving_permit');
        if($driving_license)$data['driving_license'] = $driving_license;
        else $data['driving_license'] = Func::uploadFiles('driving_license');
        $data['status'] = 0;
        $data['city_id'] = $city_id;
        $data['create_time'] = TIME_NOW;

        $driverApplyModel->set($data)->add(true);

        AJAX::success();

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

        $model = DriverModel::copyMutiInstance();
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
    function getMyInfo(DriverModel $driverModel){
        // $this->L->id = 3;
        !$this->L->id && AJAX::error('未登录');

        $info['avatar'] = $this->L->userInfo->avatar;
        $info['name'] = $this->L->userInfo->name;
        $info['sex'] = $this->L->userInfo->sex;
        $info['phone'] = $this->L->userInfo->phone;
        $info['brand'] = $this->L->userInfo->brand;
        $info['car_number'] = $this->L->userInfo->car_number;
        $info['type_driving'] = $this->L->userInfo->type_driving;
        $info['type_taxi'] = $this->L->userInfo->type_taxi;
        $info['id'] = $this->L->id;
        $info['judge_score'] = $this->L->userInfo->judge_score;
        $info['money'] = $this->L->userInfo->money;

        $info['count'] = $driverModel->select('COUNT(*) AS c','RAW')->where(['parent_id'=>$this->L->id])->find()->c;

        $info['apply_status'] = DriverApplyModel::copyMutiInstance()->where(['id'=>$this->L->id])->order('create_time desc')->find()->status;
        NULL === $info['apply_status'] && $info['apply_status'] = '-2';
        $m = DriverIncomeModel::copyMutiInstance();
        $info['money_today'] = $m->select('SUM(money) AS c','RAW')->where(['driver_id'=>$this->L->id])->where('create_time>%n',TIME_TODAY)->find()->c;
        
        if(!$info['money_today'])$info['money_today'] = '0.00';
        $info['order_today'] = TripModel::copyMutiInstance()->select('COUNT(*) AS c','RAW')->where('status>3')->where('type<3')->where(['driver_id'=>$this->L->id])->where('create_time>%n',TIME_TODAY)->find()->c;
        $out['info'] = $info;

        AJAX::success($out);
    }

    # 修改我的信息
    function changeMyInfo($name,$avatar,$sex,$car_number,$brand){

        !$this->L->id && AJAX::error('未登录');

        $name && $this->L->userInfo->name = $name;
        $avatar && $this->L->userInfo->avatar = $avatar;
        $sex != NULL && $this->L->userInfo->sex = $sex;
        $brand && $this->L->userInfo->brand = $brand;
        $car_number && $this->L->userInfo->car_number = $car_number;

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
    function getMyMessage(DriverMessageModel $model,$page = 1,$limit = 10){
        
        !$this->L->id && AJAX::error('未登录');
        $where['driver_id'] = $this->L->id;
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
    function feedback($content,DriverFeedbackModel $model){

        !$this->L->id && AJAX::error('未登录');
        !$content && AJAX::error('内容不能为空！');

        $model->set(['driver_id'=>$this->L->id,'content'=>$content,'create_time'=>TIME_NOW])->add();

        AJAX::success();

    }


    /** 获取行程 */
    function getTripList($page=1,$limit=10,TripModel $tripModel,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel){

        // $this->L->id = 3;
        !$this->L->id && AJAX::error('未登录');
        $where['driver_id'] = $this->L->id;
        $where['type'] = ['type<3'];
        $list = $tripModel->where($where)->order('create_time desc')->page($page,$limit)->get()->toArray();

        $select = ['start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,statuss,driver_id,coupon,total_fee,fee,stat.user_name,stat.driver_name'];

        foreach($list as $k=>&$v){

            

            if($v->type == 1){
                $v->orderInfo = $orderDrivingModel->select($select,'RAW')->find($v->id);
            }elseif($v->type == 2){
                $v->orderInfo = $orderTaxiModel->select($select,'RAW')->find($v->id);
            }
            
            if(!$v->orderInfo)unset($list[$k]);
            else{

                $v->orderInfo->create_date = Func::time_calculate($v->orderInfo->create_time);
                if($v->user_id){
                    $v->userInfo = UserModel::copyMutiInstance()->select('avatar','name','sex','phone')->find($v->user_id);
                    if(!$v->userInfo)$v->user_id = '0';
                }

            }
        }


        $order_count = $tripModel->select('COUNT(*) AS c','RAW')->where(['driver_id'=>$this->L->id])->where('statuss>44')->where('type<3')->find()->c;

        $out['list'] = $list;
        $out['order_count'] = $order_count;
        $out['judge_score'] = $this->L->userInfo->judge_score;
        AJAX::success($out);

    }




    /** 线下支付
     * offlinePay
     * @param mixed $id 
     * @param mixed $tripModel 
     * @param mixed $paymentModel 
     * @return mixed 
     */
    function offlinePay($id,TripModel $tripModel,PaymentModel $paymentModel){
    
        !$this->L->id && AJAX::error('未登录');


        $trip = $tripModel->find($id);

        $trip->driver_id != $this->L->id && AJAX::error('无权限');
        $trip->statuss != 45 && AJAX::error('该订单已过期');
        $trip->type == 3 && AJAX::error('error');

        $data['user_id'] = $trip->user_id;
        $data['ctime'] = TIME_NOW;
        $data['success_time'] = TIME_NOW;

        if($trip->type == 1){
            $order = Model::copyMutiInstance('order_driving')->find($trip->id);
        }elseif($trip->type == 2){
            $order = Model::copyMutiInstance('order_taxi')->find($trip->id);
        }elseif($trip->type == 3){
            $order = Model::copyMutiInstance('order_way')->find($trip->id);
        }

        !$order && AJAX::error('error');
        
        $data['total_fee'] = $order->total_fee;
        $data['out_trade_no'] = DATE_TODAY.Func::randWord(10,3);
        $data['pay_type'] = 'offline';
        $data['update_time'] = TIME_NOW;
        $data['success_date'] = date('Y-m-d',TIME_NOW);
        $data['trip_id'] = $id;

        DB::start();


        $paymentModel->set($data)->add();

        $trip->statuss = 50;
        $trip->pay_type = 2;
        $trip->save();

        if($trip->type == 1){
            Model::copyMutiInstance('order_driving')->set(['statuss'=>50])->save($trip->id);
        }elseif($trip->type == 2){
            Model::copyMutiInstance('order_taxi')->set(['statuss'=>50])->save($trip->id);
        }elseif($trip->type == 3){
            Model::copyMutiInstance('order_way')->set(['statuss'=>50])->save($trip->id);
        }



        Func::addIncome($trip->driver_id,$trip->user_id,$order,$trip->type,$trip->trip_id,1);
        

        DB::commit();

        AJAX::success();


    }



    function getJudge($page = 1 ,$limit = 10,JudgeModel $judgeModel){

        !$this->L->id && AJAX::error('未登录');

        $list = $judgeModel->select('*','user.name','user.avatar')->where('type<3')->where(['driver_id'=>$this->L->id])->order('create_time desc')->page($page,$limit)->get()->toArray();

        $out['list'] = $list;

        AJAX::success($out);


    }



    /** 我的收入
     * income
     * @param mixed $incomeModel 
     * @return mixed 
     */
    function income(DriverIncomeModel $incomeModel,$page = 1,$limit = 10){

        !$this->L->id && AJAX::error('未登录');
        $month = date('Ym');

        $week = TIME_TODAY - (date('w') - 1) * 24 * 3600;

        $money = $incomeModel->select('SUM(`money`) AS m','RAW')->where(['driver_id'=>$this->L->id,'level'=>0])->where('create_time>%n',$week)->find()->m;
        if(!$money)$money = '0.00';
        $out['month'] = $money;
        $money = $incomeModel->select('SUM(`money`) AS m','RAW')->where(['driver_id'=>$this->L->id,'level'=>0])->find()->m;
        if(!$money)$money = '0.00';
        $out['all'] = $money;

        $list = $incomeModel->select('*','trip.start_name','trip.end_name')->page($page,$limit)->where(['driver_id'=>$this->L->id,'level'=>0])->order('create_time desc')->get()->toArray();

        foreach($list as &$v){
            $v->create_date = date('m-d H:i',$v->create_time);
        }

        $out['list'] = $list;
        AJAX::success($out);
    }


    # 我的余额
    function myMoney(DriverMoneyLogModel $model,$page = 1,$limit = 20){

        !$this->L->id && AJAX::error('未登录');
        $list = $model->where(['driver_id'=>$this->L->id])->order('create_time desc')->page($page,$limit)->get()->toArray();

        $out['list'] = $list;
        $out['money'] = $this->L->userInfo->money;

        AJAX::success($out);

    }

    function myBank(DriverBankModel $model){

        !$this->L->id && AJAX::error('未登录');
        $list = $model->select('*','bank.thumb','bank.name>bank')->where(['driver_id'=>$this->L->id])->order('update_time desc')->get()->toArray();
        $out['list'] = $list;
        AJAX::success($out);

    }

    function updBank(DriverBankModel $model,$id = 0,$code,$bank_id,$bank_name,$name){

        !$this->L->id && AJAX::error('未登录');

        $data['code'] = $code;
        $data['bank_id'] = $bank_id;
        $data['bank_name'] = $bank_name;
        $data['name'] = $name;
        $data['update_time'] = TIME_NOW;
        $data['driver_id'] = $this->L->id;

        if($id){

            $b = $model->find($id);
            if($b->driver_id != $this->L->id)AJAX::error('err');
            $model->set($data)->save($id);
        }else{
            $model->set($data)->add();

        }
        AJAX::success();

    }

    function delBank(DriverBankModel $model,$id = 0){

        !$this->L->id && AJAX::error('未登录');
        $b = $model->find($id);
        if($b->driver_id != $this->L->id)AJAX::error('err');

        $model->remove($id);
        AJAX::success();

    }

    /** 申请提现
     * cashApply
     * @param mixed $model 
     * @return mixed 
     */
    function cashApply(DriverMoneyLogModel $model,$money,$bank_id){
        
        !$this->L->id && AJAX::error('未登录');

        $model->where(['driver_id'=>$this->L->id,'status'=>0])->find() && AJAX::error('已有一条提现申请正在处理中！');

        $this->L->userInfo->money < $money && AJAX::error('余额不足，无法提现');

        DB::start();
        $this->L->userInfo->money -= $money;
        $this->L->userInfo->save();

        $data['money'] = - $money;
        $data['bank_id'] = $bank_id;
        $data['create_time'] = TIME_NOW;
        $data['content'] = '提现';
        $data['driver_id'] = $this->L->id;

        $model->set($data)->add();

        DB::commit();

        AJAX::success();


    }


    function hasMessage(DriverMessageModel $model){

        !$this->L->id && AJAX::error('未登录');

        $where['driver_id'] = $this->L->id;
        $where['isread'] = 0;
        $e = $model->where($where)->find();

        if($e)$data['hasMessage'] = '1';
        else $data['hasMessage'] = '0';

        AJAX::success($data);
    }

    function push($id,$message,$type){

        // Func::push_driver($id,$message,['type'=>$type]);
        AJAX::success();
    }

    function hasDuringOrder(TripModel $model){

        !$this->L->id && AJAX::error('未登录');
        
        $where['driver_id'] = $this->L->id;
        $where['statuss'] = ['statuss IN (%c)',[5,10,20,25,30,35,40,45]];
        $where['type'] = ['type IN (%c)',[1,2]];

        $trip = $model->where($where)->find();

        if($trip){

            $out['has'] = '1';
            $out['id'] = $trip->id;
            $out['trip_id'] = $trip->trip_id;
            $out['type'] = $trip->type;
            $out['statuss'] = $trip->statuss;

        }else{
            $out['has'] = '0';

        }
        AJAX::success($out);



    }
    
    
}