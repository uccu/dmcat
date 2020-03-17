<?php

namespace App\Blog\Model;

use Model;

class ArticleModel extends Model
{
    public $table = 'article';

    function category()
    {
        return $this->join(CategoryModel::class, 'id', 'category_id', 'LEFT');
    }
}
