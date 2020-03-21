<?php

namespace App\Blog\Controller;

use Controller;
use Uccu\DmcatHttp\Response;
use App\Blog\Middleware\L;
use App\Resource\Tool\Func;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\Blog\Model\AdminModel;


class AdminController extends Controller
{


    function __construct()
    {

        $this->L = L::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
    }

    /** 给密码加密
     * 2333
     * @param mixed $password 
     * @param mixed $salt 
     * @return mixed 
     */
    public function encrypt_password($password, $salt)
    {
        return sha1($this->salt . md5($password) . $salt);
    }
    /** 生成登录TOKEN
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function encrypt_token($info)
    {
        return Func::randWord() . Func::aes_encode(Func::randWord() . base64_encode(sha1($info->password . $this->salt . TIME_NOW) . '|' . $info->id . '|' . TIME_NOW));
    }


    /** 登出
     * 
     * @return mixed 
     */
    function logout()
    {
        Response::getSingleInstance()->cookie('admin_token', '', -3600);
        header('Location:/admin/login');
    }

    /** 登录
     * 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $cookie 
     * @return mixed 
     */
    function login($phone = null, $password = null, AdminModel $model, $cookie = null)
    {


        //检查参数是否存在
        !$phone && AJAX::error('账号不能为空！');
        !$password && AJAX::error('密码不能为空！');

        //找到对应手机号的用户
        $info = $model->where('phone=%n', $phone)->find();
        !$info && AJAX::error('用户不存在');

        //是否储存登录信息到cookie
        if ($cookie) $this->cookie = true;

        # 验证密码 加密算法采用  sha1(网站干扰码+md5(密码)+用户干扰码)
        $encryptedPassword = $this->encrypt_password($password, $info->salt);
        if ($encryptedPassword != $info->password) AJAX::error('密码错误');

        !$info->active && AJAX::error('账号已被禁用，请联系管理员！');

        //输出登录返回信息
        $this->_out_info($info);
    }



    /** 输出用户登录信息
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function _out_info($info)
    {

        $admin_token = $this->encrypt_token($info);
        $this->cookie && Response::getSingleInstance()->cookie('admin_token', $admin_token, 0);

        $out = [
            'admin_token' => $admin_token,
            'id' => $info->id,
            'avatar' => $info->avatar,
            'name' => $info->name,

        ];

        AJAX::success($out);
    }

    function change_password($pwd)
    {
        !$this->L->id && AJAX::error('未登录');
        !$this->L->userInfo->type && AJAX::error('嘿嘿嘿');

        $this->L->userInfo->password = $this->encrypt_password($pwd, $this->L->userInfo->salt);
        $this->L->userInfo->save();

        Response::getSingleInstance()->cookie('user_token', '', -3600);
        AJAX::success(null, 200, '/admin/login');
    }



    # 我的信息
    function getMyInfo()
    {

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
    function changeMyInfo($name, $avatar, $sex)
    {

        !$this->L->id && AJAX::error('未登录');

        $name && $this->L->userInfo->name = $name;
        $avatar && $this->L->userInfo->avatar = $avatar;
        $sex && $this->L->userInfo->sex = $sex;

        $this->L->userInfo->save();

        AJAX::success();
    }

    # 修改头像
    function changeMyAvatar()
    {

        !$this->L->id && AJAX::error('未登录');

        $out['path'] = $path = Func::uploadFiles('avatar', 100, 100);
        !$path && AJAX::error('上传失败，没有找到上传文件！');

        $this->L->userInfo->avatar = $path;
        $this->L->userInfo->save();

        AJAX::success($out);
    }
}
