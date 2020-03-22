<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Uccu\DmcatHttp\Request;
use Uccu\DmcatTool\Tool\AJAX;

use App\Blog\Middleware\L;
use App\Admin\Tool\AdminFunc;


# 数据模型
use App\Blog\Model\ArticleModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;
use App\Blog\Model\CategoryModel;

# 管理员管理
class ArticleController extends Controller
{

    function __construct()
    {

        $this->L = L::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->controller = 'article';
    }



    function article($id = 0)
    {

        View::addData(['getList' => 'admin_' . __FUNCTION__ . '?id=' . $id]);
        View::hamlReader('home/list', 'Admin');
    }
    function admin_article(ArticleModel $model, $page = 1, $limit = 10, $id = 0)
    {
        $m = Lists::getSingleInstance($model, $page, $limit);
        # 权限
        $m->checkPermission(11);
        # 允许操作接口
        $m->setOpt('get', '../' . $this->controller . '/' . __FUNCTION__ . '_get');
        $m->setOpt('upd', '../' . $this->controller . '/' . __FUNCTION__ . '_upd');
        $m->setOpt('del', '../' . $this->controller . '/' . __FUNCTION__ . '_del');
        $m->setOpt('view', 'home/upd');
        $m->setOpt('add', 'home/upd');
        # 设置名字
        $m->setName('文章');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('标题');
        $m->setHead('分类');
        $m->setHead('封面图');
        $m->setHead('发布时间');
        $m->setHead('更新时间');
        $m->setHead('其他');
        $m->setHead('显示');
        # 设置表体
        $m->setBody('id');
        $m->setBody('title');
        $m->setBody('categoryName');
        $m->setBody(['name' => 'thumb', 'type' => 'pic']);
        $m->setBody('create_time');
        $m->setBody('update_time');
        $m->setBody(['name' => 'other', 'href' => 1, 'default' => '查看回复', 'tagName' => '查看回复']);
        $m->setBody(['name' => 'active', 'type' => 'checkbox']);
        # 筛选
        $m->where = [];
        if ($id) $m->where['id'] = $id;
        # 获取列表
        $model->select('*', 'category.name>categoryName')->order('create_time desc');
        $m->getList();
        $m->each(function ($e) {
            $e->other_href = 'reply/reply?articleId=' . $e->id;
        });
        $m->output();
    }
    function admin_article_get(ArticleModel $model, $id)
    {
        $m = Gets::getSingleInstance($model, $id);
        # 权限
        $m->checkPermission(11);
        # 允许操作接口
        $m->setOpt('upd', '../' . $this->controller . '/' . preg_replace('/get$/', 'upd', __FUNCTION__));
        $m->setOpt('back', $this->controller . '/' . preg_replace('/^admin_|_get$/', '', __FUNCTION__));
        $m->setOpt('view', 'home/upd');
        # 设置表体
        $m->setBody(['type'  =>  'hidden', 'name'  =>  'id']);
        $m->setBody(['title'  =>  '标题', 'name'  =>  'title', 'size'  =>  '6']);
        $m->setBody(['title'  =>  '描述', 'name'  =>  'description', 'type'  =>  'textarea']);

        $category = CategoryModel::clone()->getField('name', 'id')->toArray();
        $m->setBody(['title'  =>  '分类', 'name'  =>  'category_id', 'type'  =>  'radio', 'default' => '1', 'option' => $category]);
        $m->setBody(['title'  =>  '封面图', 'name'  =>  'thumb', 'size'  =>  '6']);
        $m->setBody(['title'  =>  '启用', 'name'  =>  'active', 'type'  =>  'radio', 'default' => '0', 'option' => ['0' => '关闭', '1' => '开启']]);

        $m->setBody(['title'  =>  '描述', 'name'  =>  'content', 'type'  =>  'textarea', 'size'  =>  '8']);
        # 设置名字
        $m->setName('文章管理');
        $m->getInfo();
        $m->output();
    }
    function admin_article_upd(ArticleModel $model, $id)
    {

        $this->L->adminPermissionCheck(11);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model, $id, $data);

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_article_del(ArticleModel $model, $id)
    {
        $this->L->adminPermissionCheck(11);
        $del = AdminFunc::del($model, $id);
        $out['del'] = $del;
        AJAX::success($out);
    }
}
