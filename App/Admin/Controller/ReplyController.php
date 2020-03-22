<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Uccu\DmcatHttp\Request;
use Uccu\DmcatTool\Tool\AJAX;

use App\Blog\Middleware\L;
use App\Admin\Tool\AdminFunc;
use App\Blog\Tool\Smtp;

# 数据模型
use App\Blog\Model\ArticleModel;
use App\Blog\Model\ReplyModel;

use App\Admin\Set\Gets;
use App\Admin\Set\Lists;
use App\Blog\Model\CategoryModel;

# 管理员管理
class ReplyController extends Controller
{

    function __construct()
    {

        $this->L = L::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;
        $this->email = $this->L->config->email;
        $this->controller = 'reply';
    }



    function reply($articleId = 0)
    {

        View::addData(['getList' => 'admin_' . __FUNCTION__ . '?articleId=' . $articleId]);
        View::hamlReader('home/list', 'Admin');
    }
    function admin_reply(ReplyModel $model, $page = 1, $limit = 10, $articleId = 0)
    {
        $m = Lists::getSingleInstance($model, $page, $limit);
        # 权限
        $m->checkPermission(12);
        # 允许操作接口
        $m->setOpt('get', '../' . $this->controller . '/' . __FUNCTION__ . '_get');
        $m->setOpt('upd', '../' . $this->controller . '/' . __FUNCTION__ . '_upd');
        $m->setOpt('del', '../' . $this->controller . '/' . __FUNCTION__ . '_del');
        $m->setOpt('view', 'home/upd');
        # 设置名字
        $m->setName('回复审核');
        # 设置表头
        $m->setHead('编号');
        $m->setHead('名字');
        $m->setHead('邮箱');
        $m->setHead('内容');
        $m->setHead('文章标题');
        $m->setHead('回复时间');
        $m->setHead('显示');
        # 设置表体
        $m->setBody('id');
        $m->setBody('name');
        $m->setBody('email');
        $m->setBody('comment');
        $m->setBody(['name' => 'article', 'href' => 1, 'tagName' => '查看文章']);
        $m->setBody('create_time');
        $m->setBody(['name' => 'active', 'type' => 'checkbox']);
        # 筛选
        if ($articleId) {
            $m->where = ['article_id' => $articleId];
        } else {
            $m->where = ['active' => 0];
        }

        # 获取列表
        $model->select('*', 'article.title>article')->order('create_time desc');
        $m->getList();
        $m->each(function ($e) {
            $e->article_href = 'article/article?id=' . $e->article_id;
        });
        $m->output();
    }
    function admin_reply_get(ReplyModel $model, $id)
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
        $m->setBody(['title'  =>  '名字', 'name'  =>  'name', 'size'  =>  '2']);
        $m->setBody(['title'  =>  '邮箱', 'name'  =>  'email', 'size'  =>  '2']);
        $m->setBody(['title'  =>  '文章标题', 'name'  =>  'articleTitle', 'size'  =>  '6', 'disabled' => true]);
        $m->setBody(['title'  =>  '内容', 'name'  =>  'comment', 'type'  =>  'textarea']);
        $m->setBody(['title'  =>  '启用', 'name'  =>  'active', 'type'  =>  'radio', 'default' => '0', 'option' => ['0' => '关闭', '1' => '开启']]);
        $m->setBody(['title'  =>  '发送邮件提示', 'name'  =>  'sendEmail', 'type'  =>  'radio', 'default' => '1', 'option' => ['0' => '关闭', '1' => '开启']]);
        # 设置名字
        $m->setName('回复管理');
        $model->select('*', 'article.title>articleTitle');
        $m->getInfo();
        $m->output();
    }
    function admin_reply_upd(ReplyModel $model, $id, $sendEmail, $active, $email)
    {

        $this->L->adminPermissionCheck(12);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        unset($data['id']);

        $upd = AdminFunc::upd($model, $id, $data);

        if ($sendEmail && $active) {
            $info = $model->find($id);
            if ($info && $email) {
                Smtp::send($email, '您的评论通过了审核', '<div> 您的评论已通过了审核，您可以查看文章：<a href="https://blog.yoooo.co/article/' . $info->article_id . '">查看</a></div>');
            }

            if ($info->reply_names) {
                $names = json_decode($info->reply_names);

                foreach ($names as $name) {
                    $re = $model->where(['article_id' => $info->article_id, 'name' => $name])->find();
                    if ($re && $re->email) {
                        Smtp::send($re->email, '有人回复了您的评论', '<div> ' . strip_tags($data['name']) . ' 回复了您的评论，查看文章：<a href="https://blog.yoooo.co/article/' . $info->article_id . '/comment/' . $id . '">查看</a></div>');
                    }
                }
            }
        }

        $out['upd'] = $upd;
        AJAX::success($out);
    }
    function admin_reply_del(ReplyModel $model, $id)
    {
        $this->L->adminPermissionCheck(12);
        $del = AdminFunc::del($model, $id);
        $out['del'] = $del;
        AJAX::success($out);
    }
}
