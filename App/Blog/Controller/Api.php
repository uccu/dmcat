<?php

namespace App\Blog\Controller;

use Controller;
use Uccu\DmcatTool\Tool\AJAX;

use App\Blog\Model\ArticleModel;
use App\Blog\Model\ReplyModel;
use App\Blog\Tool\Smtp;
use App\Resource\Tool\Func;

class Api extends Controller
{

    function __construct()
    {
        Func::visit_log();
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
        header('Access-Control-Allow-Headers:Origin,x-requested-with,content-type,Accept');
    }
    function menu()
    {
        AJAX::success([
            'list' => [2020, 2019]
        ]);
    }

    function getArticleList($page = 1, ArticleModel $model, $categoryId = 0, $year = 0)
    {
        if ($categoryId) {
            $model->where(['category_id' => $categoryId, 'active' => 1]);
        }
        if ($year) {
            $year = floor($year);
            if (!$year) $year = '2020';
            $model->where('%F BETWEEN %n AND %n', 'create_time', $year . '-01-01 00:00:00', $year . '-12-31 23:59:59');
        }
        $data = $model->select('id', 'title', 'description', 'thumb', 'create_time>createTime', 'view', 'reply', 'category.name>categoryName')->page($page, 10)->order('create_time desc')->get()->toArray();

        AJAX::success([
            'list' => $data,
            'year' => $year
        ]);
    }

    function getArticleInfo($id = 0, ArticleModel $model)
    {
        if (!$id) {
            AJAX::error('文章不存在！');
        }

        $info = $model->select('id', 'title', 'description', 'thumb', 'create_time>createTime', 'view', 'reply', 'category.name>categoryName', 'content')->where(['active' => 1])->find($id);

        if (!$info) {
            AJAX::error('文章不存在！');
        }
        $model->set('%F = %F + 1', 'view', 'view')->save($id);
        $info->view++;

        AJAX::success([
            'info' => $info
        ]);
    }

    function send()
    {
        $email = 'cat@4moe.com';
        // Smtp::send($email, '评论回复', '<div> <name> 回复了您的评论：</div><div></div>');
    }

    function reply($name, $comment, $id, $email, ArticleModel $model, ReplyModel $rmodel)
    {
        if (!$id) {
            AJAX::error('文章不存在！');
        }
        $info = $model->find($id);
        if (!$info) {
            AJAX::error('文章不存在！');
        }
        if (!$email) {
            AJAX::error('请填写email');
        }
        if (!$name) {
            AJAX::error('请填写名字');
        }
        if (!$comment) {
            AJAX::error('请填写内容');
        }

        $data = $rmodel->where([
            'article_id' => $id,
            'name' => $name,
            'email' => $email,
            'comment' => $comment
        ])->find();
        if ($data) {
            AJAX::error('请勿重复回复');
        }
        $replyNames = [];
        $comment = preg_replace_callback('#@([^ ]+) #', function ($m) use ($rmodel, $id, &$replyNames) {
            $reply = $rmodel->where(['article_id' => $id, 'name' => $m[1]])->find();
            if (!$reply) return '';
            array_push($replyNames, $m[1]);
            return $m[0];
        }, $comment);


        $id = $rmodel->set([
            'name' => $name,
            'email' => $email,
            'article_id' => $id,
            'comment' => $comment,
        ])->set('reply_names=%j', $replyNames)->add();

        if ($id) {
            Smtp::send('418667631@qq.com', '有个用户评论了您发的文章', '<div> ' . strip_tags($name) . ' 回复了您的文章：<a href="https://blog.yoooo.co/article/' . $info->id . '">' . $info->title . '</a></div>');
        }

        AJAX::success(['id' => $id]);
    }

    function getReplyList($id, $page, ReplyModel $model)
    {
        if (!$id) {
            AJAX::error('文章不存在！');
        }
        $model->where(['article_id' => $id, 'active' => 1]);
        $data = $model->select('id', 'name', 'email>avatar', 'comment', 'create_time>createTime')->page($page, 10)->order('create_time desc')->get()->toArray();
        foreach ($data as $v) {
            if (!$v->avatar) {
                $v->avatar = '/pic/myAvatar.jpg';
                $v->self = 1;
            } else {
                $v->avatar = 'https://secure.gravatar.com/avatar/' . md5($v->avatar) . '?s=48';
            }
        }

        AJAX::success([
            'list' => $data
        ]);
    }
}
