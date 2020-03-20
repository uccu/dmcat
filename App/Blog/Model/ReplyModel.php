<?php

namespace App\Blog\Model;

use Model;

class ReplyModel extends Model
{
    public $table = 'reply';

    function article()
    {
        return $this->join(ArticleModel::class, 'id', 'article_id', 'LEFT');
    }
}
