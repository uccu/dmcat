<?php

namespace App\Blog\Controller;

use Controller;
use AJAX;

use App\Blog\Model\ArticleModel;

class Api extends Controller
{

    function menu()
    {
        AJAX::success([
            'list' => [2020, 2019]
        ]);
    }

    function getArticleList($page = 1, ArticleModel $model, $categoryId = 0)
    {
        if ($categoryId) {
            $model->where(['category_id' => $categoryId]);
        }
        $data = $model->page($page, 10)->get()->toArray();

        AJAX::success([
            'list' => $data
        ]);
    }

    function getArticleInfo($id = 0, ArticleModel $model)
    {
        if (!$id) {
            AJAX::error('文章不存在！');
        }
        $info = $model->find($id);

        if (!$info) {
            AJAX::error('文章不存在！');
        }

        AJAX::success([
            'info' => $info
        ]);
    }
}
