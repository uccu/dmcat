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

        $where['user_id'] = $this->L->id;
        $where['isread'] = 0;
        $e = MessageModel::copyMutiInstance()->where($where)->find();

        if($e)$data['hasMessage'] = '1';
        else $data['hasMessage'] = '0';

        $out = [
            'user_token'=>$user_token,
            'id'=>$info->id,
            'avatar'=>$info->avatar,
            'name'=>$info->name,
            'type'=>$info->type,
            'hasMessage'=>$data['hasMessage']
            
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
    function register($terminal = 0,UserModel $model,$password = null,$phone = null,$phone_captcha,$cookie = false,$parent_id = 0){
        
        
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
        $info['count'] = $userModel->select('COUNT(*) AS c','RAW')->where(['parent_id'=>$this->L->id])->find()->c;

        $out['info'] = $info;

        AJAX::success($out);
    }

    # 修改我的信息
    function changeMyInfo($name,$avatar,$sex){

        !$this->L->id && AJAX::error('未登录');

        $name && $this->L->userInfo->name = $name;
        $avatar && $this->L->userInfo->avatar = $avatar;
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


    /** 获取行程 */
    function getTripList(UserCouponModel $userCouponModel,$page=1,$limit=10,TripModel $tripModel,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel){
        // $this->L->id = 47;
        !$this->L->id && AJAX::error('未登录');
        $where['user_id'] = $this->L->id;
        $list = $tripModel->where($where)->order('create_time desc')->page($page,$limit)->get()->toArray();

        $select = 'start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,status,driver_id,coupon,total_fee,fee';



        $coupon = $userCouponModel->where(['user_id'=>$this->L->id])->where('end_time>%n',TIME_NOW)->where(['type'=>$type])->order('money desc')->find();
        


        foreach($list as $k=>&$v){

            # 行程的类型
            if($v->type == 1){
                $v->orderInfo = $orderDrivingModel->select($select,'RAW')->find($v->id);
            }elseif($v->type == 2){
                $v->orderInfo = $orderTaxiModel->select($select,'RAW')->find($v->id);
            }elseif($v->type == 3){
                $v->orderInfo = $orderWayModel->select($select,'RAW')->find($v->id);
            }

            
            
            if(!$v->orderInfo)unset($list[$k]);
            else{
                if($v->status < 5){
                    $v->total_fee = $v->fee = $v->estimated_price;
                }
                if($v->status < 5 && $coupon){
                    $v->coupon = $coupon->money;
                    $v->total_fee = $v->fee - $v->coupon;
                    if($v->total_fee < 0 )$v->total_fee = '0.00';
                }
                $v->orderInfo->create_date = Func::time_calculate($v->orderInfo->create_time);
                if($v->driver_id){
                    if($v->type == 3)$v->driverInfo = UserModel::copyMutiInstance()->select('avatar','name','sex','phone','judge_score','car_number','brand')->find($v->driver_id);
                    else $v->driverInfo = DriverModel::copyMutiInstance()->select('avatar','name','sex','phone','judge_score','car_number','brand')->find($v->driver_id);
                    if(!$v->driverInfo)$v->driver_id = '0';
                    else{
                        $v->driverInfo->order_count = TripModel::copyMutiInstance()->select('COUNT(*) AS c','RAW')->where('status>3')->where('type<2')->where(['driver_id'=>$v->driver_id])->find()->c;
                    }
                }
            }
        }

        $out['list'] = $list;
        AJAX::success($out);

    }


    /** 获取行程 */
    function getDriverTripList($page=1,$limit=10,TripModel $tripModel,OrderWayModel $orderWayModel){

        // $this->L->id = 47;
        !$this->L->id && AJAX::error('未登录');
        $where['driver_id'] = $this->L->id;
        $where['type'] = 3;
        $list = $tripModel->where($where)->order('create_time desc')->page($page,$limit)->get()->toArray();

        $select = 'start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,status,driver_id';

        foreach($list as $k=>&$v){

            

            
            $v->orderInfo = $orderWayModel->select($select,'RAW')->find($v->id);
            
            
            if(!$v->orderInfo)unset($list[$k]);
            else{

                $v->orderInfo->create_date = Func::time_calculate($v->orderInfo->create_time);
                if($v->user_id){
                    $v->userInfo = UserModel::copyMutiInstance()->select('avatar','name','sex','phone')->find($v->user_id);
                    if(!$v->userInfo)$v->user_id = '0';
                }

            }
        }


        $order_count = $tripModel->select('COUNT(*) AS c','RAW')->where(['driver_id'=>$this->L->id])->where('status>3')->where('type=3')->find()->c;

        $out['list'] = $list;
        $out['order_count'] = $order_count;
        $out['judge_score'] = $this->L->userInfo->judge_score;
        AJAX::success($out);

    }


    
    /** 获取默认地址
     * getLocation
     * @param mixed $locationModel 
     * @return mixed 
     */
    function getLocation(LocationModel $locationModel){

        !$this->L->id && AJAX::error('未登录');

        $where['user_id'] = $this->L->id;
        $where['type'] = 0;

        $obj = new stdClass;
        foreach($locationModel->field as $field)$obj->$field = '';

        $home = $locationModel->where($where)->find();
        $where['type'] = 1;
        $company = $locationModel->where($where)->find();

        if(!$home)$home = $obj;
        if(!$company)$company = $obj;

        $out['home'] = $home;
        $out['company'] = $company;

        AJAX::success($out);

    }

    /** 修改地址
     * changeLocation
     * @param mixed $type 
     * @param mixed $locationModel 
     * @return mixed 
     */
    function changeLocation($type,LocationModel $locationModel){

        !$this->L->id && AJAX::error('未登录');

        $data = Request::getSingleInstance()->request($locationModel->field);
        $data['user_id'] = $this->L->id;
        $data['type'] = $type ?1:0;

        $locationModel->set($data)->add(true);

        AJAX::success();
    }


    /** 申请成为顺风车司机
     * apply
     * @param mixed $userApplyModel 
     * @param mixed $car_number 
     * @param mixed $brand 
     * @return mixed 
     */
    function apply(UserApplyModel $userApplyModel,$driving_permit,$driving_license,$car_number = '',$brand = '',$city_id = 0,$latitude,$longitude, AreaModel $areaModel){

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
        if($driving_permit)$data['driving_permit'] = $driving_permit;
        else $data['driving_permit'] = Func::uploadFiles('driving_permit');
        if($driving_license)$data['driving_license'] = $driving_license;
        else $data['driving_license'] = Func::uploadFiles('driving_license');
        $data['status'] = 0;
        $data['city_id'] = $city_id;
        $data['create_time'] = TIME_NOW;

        

        $userApplyModel->set($data)->add(true);

        AJAX::success();

    }


    /** 获取附近的司机
     * getDrivers
     * @param mixed $latitude 
     * @param mixed $longitude 
     * @return mixed 
     */
    function getDrivers($latitude = 0,$longitude = 0,DriverOnlineModel $model,$type = 0){

        $latitudeRange = [$latitude - 0.1,$latitude + 0.1];
        $longitudeRange = [$longitude - 0.1,$longitude + 0.1];

        if($type == 1)$model->where(['driver.type_driving'=>1]);
        elseif($type == 2)$model->where(['driver.type_taxi'=>1]);

        $model->where('latitude BETWEEN %a AND longitude BETWEEN %a',$latitudeRange,$longitudeRange);
        
        $list = $model->get()->toArray();
        // echo $model->sql;die();
        $out['list'] = $list;
        AJAX::success($out);

    }
    
    /** 评价
     * judge
     * @param mixed $judgeModel 
     * @param mixed $tripModel 
     * @param mixed $id 
     * @param mixed $comment 
     * @param mixed $tag 
     * @param mixed $orderDrivingModel 
     * @param mixed $orderTaxiModel 
     * @param mixed $orderWayModel 
     * @return mixed 
     */
    function judge(UserModel $userModel,DriverModel $driverModel,JudgeModel $judgeModel,TripModel $tripModel,$score,$id,$comment,$tag,OrderDrivingModel $orderDrivingModel,OrderTaxiModel $orderTaxiModel,OrderWayModel $orderWayModel){

        !$this->L->id && AJAX::error('未登录');

        $trip = $tripModel->find($id);

        !$trip && AJAX::error('行程不存在');
        $trip->user_id != $this->L->id && AJAX::error('用户不符'.$trip->user_id.','.$this->L->id);
        $trip->status != 5 && AJAX::error('已评价');

        $obj = new stdClass;

        $score = floor($score);
        if($score>5)$score = 5;

        $obj->driver_id = $trip->driver_id;
        $obj->trip_id = $id;
        $obj->comment = $comment;
        $obj->score = $score;
        $obj->tag = $tag;
        $obj->user_id = $trip->user_id;
        $obj->type = $trip->type;
        $obj->create_time = TIME_NOW;

        DB::start();

        $judgeModel->set($obj)->add();

        $trip->status = 6;
        $trip->save();

        if($trip->type == 1){

            $orderDrivingModel->set(['status'=>6])->save($trip->id);
            
        }elseif($trip->type == 2){

            $orderTaxiModel->set(['status'=>6])->save($trip->id);
            
        }elseif($trip->type == 3){

            $orderWayModel->set(['status'=>6])->save($trip->id);
            
        }

        if($trip->type != 3){
            $score = $judgeModel->select('AVG(score) AS c','RAW')->where(['driver_id'=>$trip->driver_id])->where('type<3')->find()->c;
            !$score && $score = 0;
            $driverModel->set(['judge_score'=>$score])->save($trip->driver_id);
        }else{
            $score = $judgeModel->select('AVG(score) AS c','RAW')->where(['driver_id'=>$trip->driver_id])->where('type=3')->find()->c;
            !$score && $score = 0;
            $userModel->set(['judge_score'=>$score])->save($trip->driver_id);

        }


        DB::commit();

        AJAX::success(['score'=>$score]);



    }

    /** 标签
     * tagList
     * @param mixed $model 
     * @return mixed 
     */
    function tagList(TagModel $model){

        $list = $model->get()->toArray();
        
        $out['list'] = $list;

        AJAX::success($out);

    }


    /** 顺丰车司机发布路线
     * releaseRoute
     * @return mixed 
     */
    function releaseRoute(DriverWayModel $driverWayModel,$start_latitude,$start_longitude,$end_latitude,$end_longitude,$start_name,$end_name,$time){

        !$this->L->id && AJAX::error('未登录');
        $this->L->userInfo->type != 1 && AJAX::error('请申请成为顺风车司机');


        $data['user_id'] = $this->L->id;
        $data['status'] = 1;

        $driverWayModel->where($data)->find() && AJAX::error('不能重复发布行程');


        $area = Func::getArea($end_latitude,$start_longitude);
        $area2 = Func::getArea($end_latitude,$end_longitude);
        if(!$area || !$area2)AJAX:: error('地址坐标获取失败');
        $area->city == $area2->city && AJAX::error('起始地与目的地不能同市！');

        $data['start_latitude'] = $start_latitude;
        $data['start_longitude'] = $start_longitude;
        $data['end_latitude'] = $end_latitude;
        $data['end_longitude'] = $end_longitude;
        $data['start_name'] = $start_name;
        $data['end_name'] = $end_name;
        $data['start_time'] = $time;
        

        $driverWayModel->set($data)->add();

        AJAX::success();

    }

    /** 取消顺风车路线
     * cancelRoute
     * @param mixed $driverWayModel 
     * @param mixed $id 
     * @return mixed 
     */
    function cancelRoute(DriverWayModel $driverWayModel,$id){

        !$this->L->id && AJAX::error('未登录');

        $data['user_id'] = $this->L->id;
        $data['status'] = 1;

        $driverWayModel->set(['status'=>0])->where($data)->save();

        AJAX::success();

    }


    /** 获取顺风车顺路的用户
     * getWay
     * @param mixed $driverWayModel 
     * @param mixed $orderWayModel 
     * @return mixed 
     */
    function getWay(DriverWayModel $driverWayModel,OrderWayModel $orderWayModel){

        !$this->L->id && AJAX::error('未登录');
        $this->L->userInfo->type != 1 && AJAX::error('请申请成为顺风车司机');

        $where['user_id'] = $this->L->id;
        $where['status'] = 1;

        $route = $driverWayModel->where($where)->find();
        !$route && AJAX::success('未发布行程');

        $where = [];
        $latitudeRange = [$route->start_latitude - 0.2, $route->start_latitude + 0.2];
        $longitudeRange = [$route->start_longitude - 0.2, $route->start_longitude + 0.2];

        $where['x1'] = ['start_latitude BETWEEN %a AND start_longitude BETWEEN %a',$latitudeRange,$longitudeRange];
        $where['status'] = 1;
        $list = $orderWayModel->where($where)->select(['*,ABS(%F-%f) + ABS(%F-%f) AS `mul`','start_latitude',$route->start_latitude,'start_longitude',$route->start_longitude],'RAW')->order('mul desc','RAW')->get()->toArray();
        // echo $orderWayModel->sql;die();

        foreach($list as &$v){

            if($v->user_id){
                $v->userInfo = UserModel::copyMutiInstance()->select('avatar','name','sex','phone')->find($v->user_id);
                if(!$v->userInfo)$v->user_id = '0';
            }

            $v->start_date = Func::time_calculate($v->start_time);

            $v->sil = floor(100 - $v->mul * 100) . '%';

            $v->toDistance = Func::getSDistance($route->start_latitude, $route->start_longitude,$v->start_latitude, $v->start_longitude);
            if($v->toDistance>1000)$v->toDistance = number_format($v->toDistance/1000,1,'.','').'公里';
            else $v->toDistance = $v->toDistance.'米';

        }
        $route->start_date = Func::time_calculate($route->start_time);
        $out['list'] = $list;
        $out['route'] = $route;
        AJAX::success($out);
    }


    /** 抢单
     * orderRoute
     * @return mixed 
     */
    function orderRoute(OrderWayModel $orderWayModel,$id,TripModel $tripModel){

        !$this->L->id && AJAX::error('未登录');
        $this->L->userInfo->type != 1 && AJAX::error('请申请成为顺风车司机');

        $order = $orderWayModel->find($id);
        if($order->status != 1)AJAX::error('抢单失败');

        $data['status'] = 2;
        $data['driver_id'] = $this->L->id;
        
        DB::start();
        $tripModel->set($data)->where(['type'=>3,'id'=>$id])->save();
        $data['order_time'] = TIME_NOW;
        $orderWayModel->set($data)->save($id);

        Func::push($order->user_id,'有司机接了您的订单！',['type'=>'order_order']);
        
        DB::commit();
        AJAX::success();

    }


    function getJudge($page = 1 ,$limit = 10,JudgeModel $judgeModel){

        !$this->L->id && AJAX::error('未登录');

        $list = $judgeModel->select('*','user.name','user.avatar')->where('type=3')->where(['driver_id'=>$this->L->id])->order('create_time desc')->page($page,$limit)->get()->toArray();

        $out['list'] = $list;

        AJAX::success($out);


    }



    /** 模拟支付
     * fakePay
     * @param mixed $id 
     * @param mixed $tripModel 
     * @param mixed $paymentModel 
     * @return mixed 
     */
    private function fakePay($id,TripModel $tripModel,PaymentModel $paymentModel){
    
        !$this->L->id && AJAX::error('未登录');


        $trip = $tripModel->find($id);

        $trip->user_id != $this->L->id && AJAX::error('无权限');
        $trip->status != 4 && AJAX::error('该订单已过期');


        $data['user_id'] = $this->L->id;
        $data['ctime'] = TIME_NOW;
        $data['success_time'] = TIME_NOW;
        $data['total_fee'] = 0;
        $data['out_trade_no'] = 'C'.date('YmdHis',$trip->create_time).Func::add_zero($trip->id,6);
        $data['pay_type'] = 'fake';
        $data['update_time'] = TIME_NOW;
        $data['success_date'] = date('Y-m-d',TIME_NOW);
        $data['trip_id'] = $id;

        DB::start();


        $paymentModel->set($data)->add();

        $trip->status = 5;
        $trip->save();

        if($trip->type == 1){
            Model::copyMutiInstance('order_driving')->set(['status'=>5])->save($trip->id);
        }elseif($trip->type == 2){
            Model::copyMutiInstance('order_taxi')->set(['status'=>5])->save($trip->id);
        }elseif($trip->type == 3){
            Model::copyMutiInstance('order_way')->set(['status'=>5])->save($trip->id);
        }
        

        DB::commit();

        AJAX::success();


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
        !$this->L->userInfo->type && AJAX::error('不是司机');

        $trip = $tripModel->find($id);

        $trip->driver_id != $this->L->id && AJAX::error('无权限');
        $trip->status != 4 && AJAX::error('该订单已过期');
        $trip->type != 3 && AJAX::error('error');

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
        $data['out_trade_no'] = 'C'.date('YmdHis',$trip->create_time).Func::add_zero($trip->id,6);
        $data['pay_type'] = 'offline';
        $data['update_time'] = TIME_NOW;
        $data['success_date'] = date('Y-m-d',TIME_NOW);
        $data['trip_id'] = $id;

        DB::start();


        $paymentModel->set($data)->add();

        $trip->status = 5;
        $trip->save();

        if($trip->type == 1){
            Model::copyMutiInstance('order_driving')->set(['status'=>5])->save($trip->id);
        }elseif($trip->type == 2){
            Model::copyMutiInstance('order_taxi')->set(['status'=>5])->save($trip->id);
        }elseif($trip->type == 3){
            Model::copyMutiInstance('order_way')->set(['status'=>5])->save($trip->id);
        }
        

        DB::commit();

        AJAX::success();


    }
    

    /** 我的收入
     * income
     * @param mixed $incomeModel 
     * @return mixed 
     */
    function income(IncomeModel $incomeModel,$page = 1 ,$limit = 10){

        !$this->L->id && AJAX::error('未登录');
        $month = date('Ym');

        $week = TIME_TODAY - (date('w') - 1) * 24 * 3600;

        $money = $incomeModel->select('SUM(`money`) AS m','RAW')->where('type=3')->where(['driver_id'=>$this->L->id])->where('create_time>%n',$week)->find()->m;
        if(!$money)$money = '0.00';
        $out['month'] = $money;
        $money = $incomeModel->select('SUM(`money`) AS m','RAW')->where('type=3')->where(['driver_id'=>$this->L->id])->find()->m;
        if(!$money)$money = '0.00';
        $out['all'] = $money;

        $list = $incomeModel->select('*','trip.start_name','trip.end_name')->page($page,$limit)->where('type=3')->where(['driver_id'=>$this->L->id])->order('create_time desc')->get()->toArray();

        // $list2 = [];

        foreach($list as &$v){
            $v->create_date = date('m-d H:i',$v->create_time);
        }

        $out['list'] = $list;
        AJAX::success($out);
    }


    /** 我的积分
     * score
     * @param mixed $userScoreLogModel 
     * @param mixed $page 
     * @param mixed $limit 
     * @return mixed 
     */
    function myScore(UserScoreLogModel $userScoreLogModel,$page = 1,$limit = 20){

        !$this->L->id && AJAX::error('未登录');
        $list = $userScoreLogModel->where(['user_id'=>$this->L->id])->order('create_time desc')->page($page,$limit)->get()->toArray();

        $out['list'] = $list;
        $out['score'] = $this->L->userInfo->score;

        AJAX::success($out);

    }


    function myMoney(UserMoneyLogModel $model,$page = 1,$limit = 20){

        !$this->L->id && AJAX::error('未登录');
        $list = $model->where(['user_id'=>$this->L->id])->order('create_time desc')->page($page,$limit)->get()->toArray();

        $out['list'] = $list;
        $out['money'] = $this->L->userInfo->money;

        AJAX::success($out);

    }

    function myBank(UserBankModel $model){

        !$this->L->id && AJAX::error('未登录');
        $list = $model->select('*','bank.thumb','bank.name>bank')->where(['user_id'=>$this->L->id])->order('update_time desc')->get()->toArray();
        $out['list'] = $list;
        AJAX::success($out);

    }

    function updBank(UserBankModel $model,$id = 0,$code,$bank_id,$bank_name,$name){

        !$this->L->id && AJAX::error('未登录');

        $data['code'] = $code;
        $data['bank_id'] = $bank_id;
        $data['bank_name'] = $bank_name;
        $data['name'] = $name;
        $data['update_time'] = TIME_NOW;
        $data['user_id'] = $this->L->id;

        if($id){

            $b = $model->find($id);
            if($b->user_id != $this->L->id)AJAX::error('err');
            $model->set($data)->save($id);
        }else{
            $model->set($data)->add();

        }
        AJAX::success();

    }

    function delBank(UserBankModel $model,$id = 0){

        !$this->L->id && AJAX::error('未登录');
        $b = $model->find($id);
        if($b->user_id != $this->L->id)AJAX::error('err');

        $model->remove($id);
        AJAX::success();

    }

    /** 申请提现
     * cashApply
     * @param mixed $model 
     * @return mixed 
     */
    function cashApply(UserMoneyLogModel $model,$money,$bank_id){
        
        !$this->L->id && AJAX::error('未登录');

        $this->L->userInfo->money < $money && AJAX::error('余额不足！');

        $model->where(['user_id'=>$this->L->id,'status'=>0])->find() && AJAX::error('已有一条提现申请正在处理中！');

        $data['money'] = $money;
        $data['bank_id'] = $bank_id;
        $data['create_time'] = TIME_NOW;
        $data['content'] = '提现';
        $data['user_id'] = $this->L->id;

        $model->set($data)->add();

        AJAX::success();


    }



    /** 我的优惠券
     * myCoupon
     * @param mixed $model 
     * @return mixed 
     */
    function myCoupon(UserCouponModel $model){

        !$this->L->id && AJAX::error('未登录');

        $where['user_id'] = $this->L->id;


        $list = $model->where($where)->where('end_time>%n',TIME_NOW)->order('end_time desc')->get()->toArray();

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



    function usedCarList($page = 1,$limit = 10,UsedCarModel $model,AreaModel $areaModel){

        $where['status'] = 1;

        $list = $model->where($where)->order('create_time desc')->page($page,$limit)->get()->toArray();

        foreach($list as &$v){

            $city = $areaModel->find($v->city_id);
            $v->cityName = $city->areaName;

        }

        $out['list'] = $list;
        AJAX::success($out);

    }

    function usedCarInfo($id,UsedCarModel $model,AreaModel $areaModel){

        $info = $model->find($id);
        !$info && AJAX::error('信息不存在');

        $city = $areaModel->find($info->city_id);
        $info->cityName = $city->areaName;


        $out['info'] = $info;
        AJAX::success($out);

    }

    function usedCarAdd(UsedCarModel $model){

        !$this->L->id && AJAX::error('未登录');

        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);
        $data['user_id'] = $this->L->id;
        $data['create_time'] = TIME_NOW;
        
        $upd = AdminFunc::upd($model,0,$data);
        $out['upd'] = $upd;
        AJAX::success($out);


    }



    function roadList($page = 1,$limit = 10,RoadModel $model,AreaModel $areaModel){

        // $where['status'] = 1;

        $list = $model->where($where)->order('create_time desc')->page($page,$limit)->get()->toArray();



        $out['list'] = $list;
        AJAX::success($out);

    }

    function roadInfo($id,RoadModel $model,AreaModel $areaModel){

        $info = $model->find($id);
        !$info && AJAX::error('信息不存在');



        $out['info'] = $info;
        AJAX::success($out);

    }

    function roadAdd(RoadModel $model){

        !$this->L->id && AJAX::error('未登录');

        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);
        $data['user_id'] = $this->L->id;
        $data['create_time'] = TIME_NOW;

        $upd = AdminFunc::upd($model,0,$data);
        $out['upd'] = $upd;
        AJAX::success($out);


    }

    function push($id,$message,$type){

        Func::push($id,$message,['type'=>$type]);
        AJAX::success();
    }
}