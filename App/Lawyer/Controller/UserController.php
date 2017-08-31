<?php

namespace App\Lawyer\Controller;

use Controller;

# Tool
use AJAX;
use DB;
use Request;
use stdClass;
use Response;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;
use App\Lawyer\Tool\AdminFunc;
# Model
use App\Lawyer\Model\UserModel;
use App\Lawyer\Model\UserMasterCompanyModel;
use App\Lawyer\Model\UserMasterPersonModel;
use App\Lawyer\Model\CaptchaModel;
use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\UserSchoolModel;
use App\Lawyer\Model\UploadModel;
use App\Lawyer\Model\MessageModel;
use App\Lawyer\Model\ConsultModel;
use App\Lawyer\Model\AdminMenuModel;
use App\Lawyer\Model\RefundModel;


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
        if($model->where(['phone'=>$info->phone])->find()){
            AJAX::error('手机号已存在');
        }

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
        header('Location:/admin/login');
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

        //输出登录返回信息
        $this->_out_info($info);


    }

    /** 第三方登录
     * other_login
     * @param mixed $type 
     * @param mixed $code 
     * @param mixed $cookie 
     * @return mixed 
     */
    function other_login($type,$code,$cookie = null){

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        !in_array($type,['wx','wb','qq']) && AJAX::error('不支持的登录方式！');

        !$code && AJAX::error('未知的第三方登录标示！');

        $model = UserModel::copyMutiInstance();
        $userInfo = $model->where([$type=>$code])->find();

        !$userInfo && AJAX::error('用户不存在！');

        //输出登录返回信息
        $this->_out_info($userInfo);
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
            'master_type'=>$info->master_type
            
        ];

        $out['message'] = MessageModel::copyMutiInstance()->where(['user_id'=>$info->id,'isread'=>0])->find() ?'1':'0';
        $out['consult'] = ConsultModel::copyMutiInstance()->where(['user_id'=>$info->id,'which'=>1,'isread'=>0])->find() ?'1':'0';
        
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
    function register(UserModel $model,$type = '86',$password,$phone,$phone_captcha,$cookie = false,$id = 0){
        
        if(!in_array($type,['86','61']))AJAX::error('不支持的区号！');
        
        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        Func::check_phone($phone);
        Func::check_password($password);
        Func::check_phone_captcha($phone,$phone_captcha);

        $info           = new stdClass;

        if($id){
            $master = $model->find($id);
            !$master && AJAX::error('平台大使不存在！');
            $master->type == -1 && AJAX::error('推荐人并不是平台大使！');
            $info->master_id = $id;
        }
        $info->phone_type= $type;
        $info->phone    = $phone;
        $info->salt     = Func::randWord(6);
        $info->password = $this->encrypt_password($password,$info->salt);
        $this->_add_user($info);
    }

    
    /** 第三方注册
     * other_register
     * @param mixed $type 
     * @param mixed $code 
     * @param mixed $phone 
     * @param mixed $phone_captcha 
     * @param mixed $cookie 
     * @return mixed 
     */
    function other_register($password,$type,$code,$phone,$phone_captcha,$cookie = false){

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        !in_array($type,['wx','wb','qq']) && AJAX::error('不支持的登录方式！');

        Func::check_password($password);
        
        Func::check_phone_captcha($phone,$phone_captcha);

        $model = UserModel::copyMutiInstance();
        $model->where([$type=>$code])->find() && AJAX::error('已绑定账号，请直接登录！');

        
        if($userInfo = $model->where(['phone'=>$phone])->find()){
        
            $userInfo->$type && AJAX::error('已绑定该第三方登录，请解绑后重新绑定！');
            !$code && AJAX::error('未知的第三方登录标示！');

            $encryptedPassword = $this->encrypt_password($password,$userInfo->salt);
            if($encryptedPassword != $userInfo->password)AJAX::error('密码错误');

            $userInfo->$type = $code;
            $userInfo->save();

            $this->_out_info($userInfo);

        }else{
    
            $info           = new stdClass;
            $info->phone    = $phone;
            $info->name     = $phone;
            $info->salt     = Func::randWord(6);
            $info->$type    = $code;
            $info->password = $this->encrypt_password($password,$info->salt);

            $this->_add_user($info);
        }

        
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

    function change_password($pwd){
        !$this->L->id && AJAX::error('未登录');
        !$this->L->userInfo->type && AJAX::error('嘿嘿嘿');

        $this->L->userInfo->password = $this->encrypt_password($pwd,$this->L->userInfo->salt);
        $this->L->userInfo->save();

        Response::getSingleInstance()->cookie('user_token','',-3600);
        AJAX::success(null,200,'/admin/login');
    }


    /** 解除第三方绑定
     * unlink
     * @param mixed $type 
     * @return mixed 
     */
    function unlink($type){

        !$this->L->id && AJAX::error('未登录');
        !in_array($type,['wx','wb','qq']) && AJAX::error('不支持的登录方式！');

        $this->L->userInfo->$type = '';
        $this->L->userInfo->save();

        AJAX::success();

    }


    /** 更换手机号
     * change_phone
     * @param mixed $phone 手机号
     * @param mixed $phone_captcha 手机验证码 
     * @return mixed 
     */
    function change_phone($phone,$phone_captcha){

        !$this->L->id && AJAX::error('未登录');
        Func::check_phone_captcha($phone,$phone_captcha);

        $phone == $this->L->userInfo->phone && AJAX::error('修改的手机号与原手机号一致！');

        $model = UserModel::copyMutiInstance();
        $model->where([
            'phone'=>$phone
        ])->find() && AJAX::error('该手机号已经注册！');

        $this->L->userInfo->phone = $phone;
        $this->L->userInfo->save();

        AJAX::success();


    }

    
    /** 发送手机验证码
     * captcha
     * @param mixed $phone 手机号
     * @param mixed $out 是否输出AJAX
     * @return mixed 
     */
    function captcha($phone,$type = '86',$out = 1) {

        if(!in_array($type,['86','61']))AJAX::error('不支持的区号！');

        Func::check_phone($phone);
        Func::msm($phone,$type);


        if($out)AJAX::success();

    }


    /** 获取我的资料
     * getMyInfo
     * @return mixed 
     */
    function getMyInfo(){

        !$this->L->id && AJAX::error('未登录');

        $out['info']['id'] = $this->L->id;
        $out['info']['avatar'] = $this->L->userInfo->avatar;
        $out['info']['name'] = $this->L->userInfo->name;
        $out['info']['phone'] = $this->L->userInfo->phone;
        $out['info']['wx'] = $this->L->userInfo->wx;
        $out['info']['wb'] = $this->L->userInfo->wb;
        $out['info']['qq'] = $this->L->userInfo->qq;

        AJAX::success($out);

    }

    function getStatus(){

        !$this->L->id && AJAX::error('未登录');

        $out['message'] = MessageModel::copyMutiInstance()->where(['user_id'=>$this->L->id,'isread'=>0])->find() ?'1':'0';
        $out['consult'] = ConsultModel::copyMutiInstance()->where(['user_id'=>$this->L->id,'which'=>1,'isread'=>0])->find() ?'1':'0';

        AJAX::success($out);
    }


    /** 修改用户名
     * change_name
     * @param mixed $name 
     * @return mixed 
     */
    function change_name($name){
        !$this->L->id && AJAX::error('未登录');
        !$name && AJAX::error('昵称不能为空！');

        $this->L->userInfo->name = $name;
        $this->L->userInfo->save();

        AJAX::success();

    }


    /** 修改头像
     * change_avatar
     * @return mixed 
     */
    function change_avatar(){
        !$this->L->id && AJAX::error('未登录');

        $out['path'] = $path = Func::uploadFiles('avatar',100,100);
        !$path && AJAX::error('上传失败，没有找到上传文件！');
        
        $this->L->userInfo->avatar = $path;
        $this->L->userInfo->save();

        AJAX::success($out);

    }



    /** 绑定第三方
     * other_register
     * @param mixed $type 
     * @param mixed $code 
     * @param mixed $phone 
     * @param mixed $phone_captcha 
     * @param mixed $cookie 
     * @return mixed 
     */
    function bind($type,$code){
        !$this->L->id && AJAX::error('未登录');
        !in_array($type,['wx','wb','qq']) && AJAX::error('不支持的登录方式！');
        

        $model = UserModel::copyMutiInstance();
        $userInfo = $this->L->userInfo;
        
        $userInfo->$type && AJAX::error('已绑定该第三方登录，请解绑后重新绑定！');
        !$code && AJAX::error('未知的第三方登录标示！');

        $model->where([$type=>$code])->find() && AJAX::error('已绑定账号，请直接登录！');

        $userInfo->$type = $code;
        $userInfo->save();

        AJAX::success();
        
        

        
    }

    
    /** 查看是否有绑定的账号
     * checkBind
     * @param mixed $type 
     * @param mixed $code 
     * @return mixed 
     */
    function checkBind($type,$code){

        !in_array($type,['wx','wb','qq']) && AJAX::error('不支持的登录方式！');

        !$code && AJAX::error('未知的第三方登录标示！');

        $model = UserModel::copyMutiInstance();
        $userInfo = $model->where([$type=>$code])->find();

        $exist = $userInfo ? '1' : '0';

        AJAX::success(['exist'=>$exist]);

    }




    /** 获取我的VIP
     * getMyVip
     * @param mixed $model 
     * @return mixed 
     */
    function getMyVip(UserConsultLimitModel $model){

        !$this->L->id && AJAX::error('未登录');

        $where['user_id'] = $this->L->id;


        $where['rule.type'] = 0;
        $info = $model->where($where)->find();
        if(!$info || $info->word_count == 0 || $info->question_count == 0 || $info->death_time < TIME_NOW)$out['vip0']['vip'] = '0';
        else{
            $out['vip0']['vip'] = '1';
            $out['vip0']['vipInfo'] = $info;
        }

        $where['rule.type'] = 1;
        $info = $model->where($where)->find();
        if(!$info || $info->word_count == 0 || $info->question_count == 0 || $info->death_time < TIME_NOW)$out['vip1']['vip'] = '0';
        else{
            $out['vip1']['vip'] = '1';
            $out['vip1']['vipInfo'] = $info;
        }

        $where['rule.type'] = 2;
        $info = $model->where($where)->find();
        if(!$info || $info->word_count == 0 || $info->question_count == 0 || $info->death_time < TIME_NOW)$out['vip2']['vip'] = '0';
        else{
            $out['vip2']['vip'] = '1';
            $out['vip2']['vipInfo'] = $info;
        }

        AJAX::success($out);

    }


    /** 获取我的消息
     * myMessage
     * @param mixed $model 
     * @param mixed $page 
     * @param mixed $limit 
     * @return mixed 
     */
    function myMessage(MessageModel $model,$page = 1,$limit = 10){
        
        !$this->L->id && AJAX::error('未登录');
        $where['user_id'] = $this->L->id;
        $list = $model->where($where)->order('create_time desc')->page($page,$limit)->get()->toArray();

        $model->where($where)->set(['isread'=>1])->save();
        
        $out['list'] = $list;
        AJAX::success($out);
    }


    /** 我的学校列表
     * mySchool
     * @return mixed 
     */
    function mySchool(UserSchoolModel $model){

        !$this->L->id && AJAX::error('未登录');
        $list = $model->select('school.name','school.id>school_id','school.pic','school.description','id')->where(['user_id'=>$this->L->id])->get()->toArray();
        $out['list'] = $list;

        AJAX::success($out);

    }

    /** 我的学校详情
     * mySchoolDetail
     * @return mixed 
     */
    function mySchoolDetail(UserSchoolModel $model,$id,UploadModel $umodel){

        !$this->L->id && AJAX::error('未登录');
        $info = $model->select('school.name','school.id>school_id','school.pic','school.description','id','progress','file')->where(['user_id'=>$this->L->id])->find($id);
        if(!$info)AJAX::error('不存在！');

        $file = $umodel->find($info->file);
        if(!$file)$info->download = '';
        else{
            $arr = explode('.',$file->name);$str = end($arr);
            // $info->download = 'download/file/'.$info->file.'/'.$file->name;
            $info->download = 'download/file/'.$info->file.'/'.TIME_NOW.'.'.$str;
        }

        
        
        $out['info'] = $info;

        AJAX::success($out);

    }


    /** 增加用户价值
     * addValue
     * @param mixed $model 
     * @param mixed $id 
     * @param mixed $input 
     * @return mixed 
     */
    function addValue(UserModel $model,$id,$input){
        $this->L->adminPermissionCheck(68);
        $user = $model->find($id);

        !$user && AJAX::error('用户不存在！');
        !$input && AJAX::error('内容不能为空');

        DB::start();

        $user->value += $input;
        $user->save();

        if($user->master_id){

            $master = $model->find($user->master_id);

            if(in_array($master->master_type,[0,1,2])){

                $profit_0 = $this->L->config->profit_0;
                Func::addProfit($user->id,$master->id,$input * $profit_0 / 100);

                if($master->parent_id){

                    $profit_1 = $this->L->config->profit_1;
                    Func::addProfit($user->id,$master->parent_id,$input * $profit_1 / 100,$master->id);

                }

            }

        }
        DB::commit();
        
        AJAX::success();

    }


    /** 退款申请
     * applyRefund
     * @param mixed $model 
     * @return mixed 
     */
    function applyRefund(RefundModel $model,$pic,$type){

        !$this->L->id && AJAX::error('未登录');

        $data['user_id'] = $this->L->id;
        $data['state'] = 0;


        $model->where($data)->find() && AJAX::error('申请失败，您已经有一个待处理的退款申请！');

        // $paths = Func::uploadFiles();

        // !$paths && AJAX::error('必须上传至少一张图片！');

        // $data['pic'] = implode(',',$paths);

        $data['pic'] = $pic;
        $data['type'] = $type;
        $data['create_time'] = TIME_NOW;

        $model->set($data)->add();

        AJAX::success();
        
    }




    # 管理用户
    function admin_user(UserSchoolModel $smodel,UserModel $model,UserConsultLimitModel $lmodel,ConsultModel $consultModel,$page = 1,$limit = 10,$search,$type,$master_type=-2){
        
        $this->L->adminPermissionCheck(68);

        $name = '用户';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/user/admin_user_get',
                'upd'   => '/user/admin_user_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/user/admin_user_del',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search'
                    ],
                    // [
                    //     'title'=>'测试',
                    //     'name'=>'test',
                    //     'type'=>'checkbox',
                        
                    // ],
                    [
                        'title'=>'类型',
                        'name'=>'type',
                        'type'=>'select',
                        'option'=>[
                            '0'=>'全部',
                            '1'=>'普通会员',
                            '2'=>'法律会员',
                            '3'=>'留学转学会员',
                            '4'=>'签证会员',
                        ],
                        'default'=>'0'
                    ],
                    [
                        'title'=>'平台大使',
                        'name'=>'master_type',
                        'type'=>'select',
                        'option'=>[
                            '-2'=>'全部',
                            '0'=>'零级平台大使',
                            '1'=>'一级平台大使',
                            '2'=>'二级平台大使',
                            '-1'=>'普通用户',
                        ],
                        'default'=>'-2'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '手机号',
                '名字',
                '启用',
                '绑定的律师',
                '学校'

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'id',
                'phone',
                'name',
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],
                [
                    'name'=>'lawyer',
                    'href'=>true,
                ],
                [
                    'name'=>'school',
                    'href'=>true,
                ],

            ];
            

        # 列表内容
        $where = [];
        $where['type'] = 0;
        if($type){
            if($type == 2){
                $where['lim.rule.type'] = 0; 
                $where['e2'] = ['%F > %n','lim.death_time',TIME_NOW]; 
            }elseif($type == 3){
                $where['lim.rule.type'] = 1; 
                $where['e2'] = ['%F > %n','lim.death_time',TIME_NOW]; 
            }elseif($type == 4){
                $where['lim.rule.type'] = 2; 
                $where['e2'] = ['%F > %n','lim.death_time',TIME_NOW]; 
            }elseif($type == 1){
                $where['e2'] = ['NOT EXISTS(SELECT `id` FROM '.$lmodel->table.' WHERE `user_id` = %F)','id'];
            }
            

        }
        
        if($master_type != -2){
            $where['master_type'] = $master_type; 
            if($master_type == -1){
                $where['master_type'] = ['%F NOT IN (%c)','master_type',[0,1,2,3,4]];
            }
        }
        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->lawyer = '<i class="fa fa-pencil text-navy"></i> 查看';
            $v->lawyer_href = 'chat/user_chat?id='.$v->id;
            $v->school = '<i class="fa fa-pencil text-navy"></i> 查看';
            $v->school_href = 'school/user_school?id='.$v->id;

            $where2['user_id'] = $v->id;
            $consultModel->distinct();
            $lawyer_list = $consultModel->where($where2)->get_field('lawyer_id')->toArray();
            $count = count($lawyer_list);
            $v->lawyer .= '(<span style="'.($count?'color:red':'').'">'.$count.'</span>)';

            $count = $smodel->where($where2)->select('COUNT(*) AS c','RAW')->find()->c;
            $v->school .= '(<span style="'.($count?'color:red':'').'">'.$count.'</span>)';
            
        }

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
    function admin_user_get(UserModel $model,$id){

        $this->L->adminPermissionCheck(68);
        $model->find($id)->type > 0 && AJAX::error('无权限！');
        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/user/admin_user_get',
                'upd'   => '/user/admin_user_upd',
                'back'  => 'staff/user',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/user/admin_user_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '手机号',
                    'name'  =>  'phone',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'avatar',
                    'type'  =>  'avatar',
                ],
                [
                    'title' =>  '平台大使',
                    'name'  =>  'master_type',
                    'type'  =>  'select',
                    'option'=>[
                        '0'=>'零级平台大使',
                        '1'=>'一级平台大使',
                        '2'=>'二级平台大使',
                        '-1'=>'普通用户',
                    ],
                ],
                [
                    'title' =>  '微信第三方标示',
                    'name'  =>  'wx',
                ],
                [
                    'title' =>  '微博第三方标示',
                    'name'  =>  'wb',
                ],
                [
                    'title' =>  'QQ第三方标示',
                    'name'  =>  'qq',
                ],
                [
                    'title' =>  '修改密码',
                    'name'  =>  'pwd',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '总贡献价值',
                    'name'  =>  'value',
                    'disabled'=>true,
                    'size'=>'2',
                ],
                [
                    'title' =>  '贡献价值',
                    'button'  =>  '添加',
                    'type'  =>  'ajax',
                    'url'=>'/user/addValue',
                    'default'=>'0',
                    'refresh'=>true
                ],
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_user_upd(UserModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(68);
        $model->find($id)->type > 0 && AJAX::error('无权限！');
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['type']);
        unset($data['salt']);
        unset($data['id']);

        $model->where('phone = %n AND id != %d',$data['phone'],$id)->find() && AJAX::error('手机号已存在，请更改为其他手机号！');

        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_user_del(UserModel $model,$id){
        $this->L->adminPermissionCheck(68);
        $model->find($id)->type > 0 && AJAX::error('无权限！');
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

    # 管理管理员
    function admin_master(UserModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(67);

        $name = '管理员';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/user/admin_master_get',
                'upd'   => '/user/admin_master_upd',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/user/admin_master_del',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search'
                    ],
                ]

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '账号',
                '名字',
                '用户类型',
                '启用',
                '权限',
                

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'id',
                'phone',
                'name',
                'type_name',
                [
                    'name'=>'active',
                    'type'=>'checkbox',
                ],
                [
                    'name'=>'auth',
                    'href'=>true,
                ],
                

            ];
            

        # 列表内容
        $where = [];
        $where['type'] = ['%F in (1,2)','type'];

        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->auth = '<i class="fa fa-pencil text-navy"></i> 权限';
            $v->type_name = $v->type == 2?'受限管理员':'管理员';
            if($v->type == 2)$v->auth_href = 'staff/auth?id='.$v->id;
            else $v->auth = '所有权限';
        }

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
    function admin_master_get(UserModel $model,$id){

        $this->L->adminPermissionCheck(67);

        $name = '用户管理';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/user/admin_master_get',
                'upd'   => '/user/admin_master_upd',
                'back'  => 'staff/master',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/user/admin_master_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '账号',
                    'name'  =>  'phone',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '名字',
                    'name'  =>  'name',
                    'size'  =>  '4',
                ],
                [
                    'title' =>  '头像',
                    'name'  =>  'avatar',
                    'type'  =>  'avatar',
                ],
                [
                    'title' =>  '用户类型',
                    'name'  =>  'type',
                    'type'  =>  'select',
                    'option'=>[
                        '1'=>'管理员',
                        '2'=>'受限管理员',
                    ],
                ],
                [
                    'title' =>  '修改密码',
                    'name'  =>  'pwd',
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
    function admin_master_upd(UserModel $model,$id,$pwd,$type){
        $this->L->adminPermissionCheck(67);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['type']);
        unset($data['salt']);
        unset($data['id']);

        $model->where('phone = %n AND id != %d',$data['phone'],$id)->find() && AJAX::error('账号已存在，请更改为其他账号！');

        if($type == 2)$data['type'] = 2;
        else $data['type'] = 1;
        if(!$id){
            $data['salt'] = Func::randWord(6);
            $data['password'] = $this->encrypt_password($pwd,$data['salt']);
        }elseif($pwd){
            $salt = $model->find($id)->salt;
            $data['password'] = $this->encrypt_password($pwd,$salt);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_master_del(UserModel $model,$id){
        $this->L->adminPermissionCheck(67);
        $del = AdminFunc::del($model,$id);
        $out['del'] = $del;
        AJAX::success($out);
    }

    # 申请成为平台大使的管理
    function admin_apply(UserModel $model,$page = 1,$limit = 10,$search,$master_type=0){
        
        $this->L->adminPermissionCheck(102);

        $name = '申请';
        # 允许操作接口
        $opt = 
            [
                'get'   => '/user/admin_apply_get',
                'upd'   => '/user/admin_apply_upd',
                'view'  => 'home/upd',

                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search'
                    ],
                    [
                        'title'=>'平台大使',
                        'name'=>'master_type',
                        'type'=>'select',
                        'option'=>[
                            '0'=>'全部',
                            '3'=>'审核中',
                            '4'=>'审核失败',
                        ],
                        'default'=>'0'
                    ],
                ]

            ];

        # 头部标题设置
        $thead = 
            [

                '',
                '用户ID',
                '账号',
                '名字',
                '状态',

            ];


        # 列表体设置
        $tbody = 
            [

                [
                    'name'=>'fullPic',
                    'type'=>'pic',
                    'href'=>false,
                    'size'=>'30',
                ],
                'id',
                'phone',
                'name',
                'status',

            ];
            

        # 列表内容
        $where = [];
        $where['type'] = 0;

        if($master_type)$where['master_type'] = $master_type;
        else{
            $where['master_type'] = ['%F IN (%c)','master_type',[3,4]];
        }
        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('master_type','create_time desc')->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
            $v->status = $v->master_type == 3 ? '未审核':'审核驳回';
        }

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
    function admin_apply_get(UserModel $model,$id,UserMasterCompanyModel $userMasterCompanyModel,UserMasterPersonModel $userMasterPersonModel){

        $this->L->adminPermissionCheck(102);

        $name = '申请';

        # 允许操作接口
        $opt = 
            [
                'get'   => '/user/admin_apply_get',
                'upd'   => '/user/admin_apply_upd',
                'back'  => 'staff/apply',
                'view'  => 'home/upd',
                'add'   => 'home/upd',
                'del'   => '/user/admin_apply_del',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '状态',
                    'name'  =>  'status',
                    'type'  =>  'select',
                    'option'=>[
                        '0'=>'未审核',
                        '1'=>'通过',
                        '2'=>'不通过',
                    ],
                    'default'=>'0'
                ],
                [
                    'title' =>  '不通过的理由',
                    'name'  =>  'reason',
                    'type'  =>  'textarea',
                ],
                
                

            ];


        

        !$model->field && AJAX::error('字段没有公有化！');

        $info2 = $model->find($id);
        $reason = $info2->reason;
        $master_type = $info2->master_type;
        
        # 是否是公司
        if($info2->master_company){

            $info = AdminFunc::get($userMasterCompanyModel,$id);
            $info->reason = $reason?$reason:'';
            $info->status = $master_type == 3 ? '0':'2';

            $tbody = array_merge($tbody,[

                [
                    'title'=>'公司名称',
                    'name'=>'name',
                    'disabled'=>true
                ],
                [
                    'title'=>'公司执照编号',
                    'name'=>'license',
                    'disabled'=>true
                ],
                [
                    'title'=>'公司税务编号',
                    'name'=>'tax',
                    'disabled'=>true
                ],
                [
                    'title'=>'座机',
                    'name'=>'cell',
                    'disabled'=>true
                ],
                [
                    'title'=>'负责人',
                    'name'=>'person',
                    'disabled'=>true
                ],
                [
                    'title'=>'联系电话',
                    'name'=>'phone',
                    'disabled'=>true
                ],
                [
                    'title'=>'详细地址',
                    'name'=>'address',
                    'disabled'=>true
                ],
                [
                    'title'=>'上一级的ID',
                    'name'=>'parent_id',
                    'disabled'=>true
                ]

            ]);
        }else{
            $info = AdminFunc::get($userMasterPersonModel,$id);
            $info->reason = $reason?$reason:'';
            $info->status = $master_type == 3 ? '0':'2';

            $tbody = array_merge($tbody,[

                [
                    'title'=>'个人姓名',
                    'name'=>'name',
                    'disabled'=>true
                ],
                [
                    'title'=>'联系电话',
                    'name'=>'phone',
                    'disabled'=>true
                ],
                [
                    'title'=>'身份证件类型',
                    'name'=>'code_type',
                    'type'=>'select',
                    'option'=>[
                        '0'=>'护照',
                        '1'=>'身份证',
                        '2'=>'澳洲驾照号'
                    ],
                    'disabled'=>true
                ],
                [
                    'title'=>'证件号',
                    'name'=>'code',
                    'disabled'=>true
                ],
                [
                    'title'=>'详细地址',
                    'name'=>'address',
                    'disabled'=>true
                ],
                [
                    'title'=>'上一级的ID',
                    'name'=>'parent_id',
                    'disabled'=>true
                ]

            ]);
        }

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    function admin_apply_upd(UserModel $model,$id,$reason,$status,UserMasterCompanyModel $userMasterCompanyModel,UserMasterPersonModel $userMasterPersonModel){

        $this->L->adminPermissionCheck(102);

        $user = $model->find($id);

        !$user && AJAX::error('用户不存在');

        if($status == 1){
            
            if($user->master_company){
                $us = $userMasterCompanyModel->find($id);
                if($us){
                    $pa = $model->find($us->parent_id);
                    if($pa->master_type == 0){

                        $user->master_type = 1;

                    }elseif($pa->master_type == 1){

                        $user->master_type = 2;
                        
                    }else{
                        AJAX::error('上一级不存在！');
                    }
                    $user->parent_id = $us->parent_id;
                    $user->save();
                }else{
                    AJAX::error('上一级不存在！');
                }
            }else{
                $us = $userMasterPersonModel->find($id);
                if($us){
                    $pa = $model->find($us->parent_id);
                    if($pa->master_type == 0){

                        $user->master_type = 1;

                    }elseif($pa->master_type == 1){

                        $user->master_type = 2;
                        
                    }else{
                        AJAX::error('上一级不存在！');
                    }
                    $user->parent_id = $us->parent_id;
                    $user->save();
                }else{
                    AJAX::error('上一级不存在！');
                }
            }
        }elseif($status == 2){
        
            $user->master_type = 4;
            $user->reason = $reason;
            !$reason && AJAX::error('理由不能为空！');
            $user->save();

        }else{

            $user->master_type = 3;
            $user->save();

        }
        
        
        AJAX::success($out);
    }

    # 修改后台权限
    function changeAuth($user_id,$id,$auth){


        $this->L->adminPermissionCheck(67);

        $model = AdminMenuModel::copyMutiInstance();

        $w = $model->find($id);

        if(!$w)AJAX::success('无模块');

        $auth_user = $w->auth_user ? explode(',',$w->auth_user) : [];
        $key = array_search($user_id,$auth_user);

        if($auth){
            
            if($key === false)$auth_user[] = $user_id;
        }else{
            
            if($key !== false)unset($auth_user[$key]);
        }
        

        $w->auth_user = $auth_user ? implode(',',$auth_user) : '';

        $w->save();

        AJAX::success();
    }



    function test(){

        Func::push(10,'test!!!');
    }
}