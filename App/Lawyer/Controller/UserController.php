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
use App\Lawyer\Model\CaptchaModel;
use App\Lawyer\Model\UserConsultLimitModel;
use App\Lawyer\Model\UserSchoolModel;
use App\Lawyer\Model\UploadModel;
use App\Lawyer\Model\MessageModel;


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
            'name'=>$info->name
            
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
    function register($password,$phone,$phone_captcha,$cookie = false){
        
        
        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        Func::check_phone($phone);

        Func::check_password($password);

        Func::check_phone_captcha($phone,$phone_captcha);

        $info           = new stdClass;
        $info->phone    = $phone;
        $info->name     = $phone;
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
    function captcha($phone,$out = 1) {

        Func::check_phone($phone);
        Func::msm($phone);


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



    function admin_user(UserModel $model,UserConsultLimitModel $lmodel,$page = 1,$limit = 10,$search,$type){
        
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
                '启用',

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

            ];
            

        # 列表内容
        $where = [];
        $where['type'] = 1;

        if($search){
            $where['search'] = ['name LIKE %n OR phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();

        foreach($list as &$v){
            $v->fullPic = $v->avatar ? Func::fullPicAddr($v->avatar) : Func::fullPicAddr('noavatar.png');
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
    function admin_master_upd(UserModel $model,$id,$pwd){
        $this->L->adminPermissionCheck(67);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['type']);
        unset($data['salt']);
        unset($data['id']);

        $model->where('phone = %n AND id != %d',$data['phone'],$id)->find() && AJAX::error('账号已存在，请更改为其他账号！');

        $data['type'] = 1;
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




    function test(){

        Func::push(10,'test!!!');
    }
}