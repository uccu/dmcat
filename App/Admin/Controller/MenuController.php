<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Uccu\DmcatHttp\Request;
use Uccu\DmcatTool\Tool\AJAX;

use App\Blog\Middleware\L;
use App\Admin\Tool\AdminFunc;


# 数据模型
use App\Blog\Model\AdminMenuModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;

# 管理员管理
class MenuController extends Controller
{

    function __construct()
    {

        $this->L = L::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'menu';
    }



    function menu()
    {

        View::addData(['getList' => 'admin_' . __FUNCTION__]);
        View::hamlReader('home/list', 'Admin');
    }
    function admin_menu(AdminMenuModel $model, $page = 1, $limit = 10)
    {
        $m = Lists::getSingleInstance($model, $page, $limit);
        # 权限
        $m->checkPermission(1);
        # 允许操作接口
        $m->setOpt('get', '../' . $this->controller . '/' . __FUNCTION__ . '_get');
        $m->setOpt('upd', '../' . $this->controller . '/' . __FUNCTION__ . '_upd');
        $m->setOpt('del', '../' . $this->controller . '/' . __FUNCTION__ . '_del');
        $m->setOpt('view', 'home/upd');
        $m->setOpt('add', 'home/upd');
        # 设置名字
        $m->setName('后台菜单');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('标题');
        $m->setHead('权限');
        $m->setHead('用户');
        $m->setHead('图标');
        $m->setHead('连接');
        $m->setHead('上一级');
        $m->setHead('启用');
        # 设置表体
        $m->setBody('id');
        $m->setBody('name');
        $m->setBody('auth');
        $m->setBody('auth_user');
        $m->setBody('fa_ico');
        $m->setBody('href');
        $m->setBody('top');
        $m->setBody(['name' => 'active', 'type' => 'checkbox']);
        # 筛选
        $m->where = [];
        # 获取列表
        $m->getList();
        $m->output();
    }
    function admin_menu_get(AdminMenuModel $model, $id)
    {
        $m = Gets::getSingleInstance($model, $id);
        # 权限
        $m->checkPermission(1);
        # 允许操作接口
        $m->setOpt('upd', '../' . $this->controller . '/' . preg_replace('/get$/', 'upd', __FUNCTION__));
        $m->setOpt('back', $this->controller . '/' . preg_replace('/^admin_|_get$/', '', __FUNCTION__));
        $m->setOpt('view', 'home/upd');
        # 设置表体
        $m->setBody(['type'  =>  'hidden', 'name'  =>  'id']);
        $m->setBody(['title'  =>  '标题', 'name'  =>  'name', 'size'  =>  '4']);
        $m->setBody(['title'  =>  '权限', 'name'  =>  'auth', 'size'  =>  '4']);
        $m->setBody(['title'  =>  '用户', 'name'  =>  'auth_user', 'size'  =>  '4']);
        $m->setBody(['title'  =>  '图标', 'name'  =>  'fa_ico', 'size'  =>  '4']);
        $m->setBody(['title'  =>  '连接', 'name'  =>  'href', 'size'  =>  '4']);
        $m->setBody(['title'  =>  '上一级', 'name'  =>  'top', 'size'  =>  '4', 'default' => '0']);
        $m->setBody(['title'  =>  '启用', 'name'  =>  'active', 'type'  =>  'radio', 'default' => '0', 'option' => ['0' => '关闭', '1' => '开启']]);
        # 设置名字
        $m->setName('后台菜单管理');
        $m->getInfo();
        $m->output();
    }
    function admin_menu_upd(AdminMenuModel $model, $id)
    {

        $this->L->adminPermissionCheck(1);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model, $id, $data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_menu_del(AdminMenuModel $model, $id)
    {
        $this->L->adminPermissionCheck(1);
        $del = AdminFunc::del($model, $id);
        $out['del'] = $del;
        AJAX::success($out);
    }
}
