<?php

namespace App\Blog\Controller;

use Controller;
use AJAX;

use App\Blog\Model\ArticleModel;

class Api extends Controller
{

    function __construct()
    {
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
            $model->where(['category_id' => $categoryId]);
        }
        if ($year) {
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

        $info = $model->select('id', 'title', 'description', 'thumb', 'create_time>createTime', 'view', 'reply', 'category.name>categoryName', 'content')->find($id);

        if (!$info) {
            AJAX::error('文章不存在！');
        }
        $model->set('%F = %F + 1', 'view', 'view')->save($id);
        $info->view++;

        AJAX::success([
            'info' => $info
        ]);
    }
}
