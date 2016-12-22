<?php

namespace App\Resource\Model;
use Model;

class ThemeModel extends Model{

    public $table = 'theme';

    protected $field = ['id','name','newname','content','last_number',
    'change_time','tags','matches','visible','level','ctime','season'];

    

    function updateMatches(){



    }
    

    
}