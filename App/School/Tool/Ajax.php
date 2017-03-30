<?php
namespace App\School\Tool;

use AJAX as A;
use App\School\Middleware\L;

class Ajax extends A{
    
    static function  error_i18n($name){

        self::error( L::getInstance()->i18n->errorMessage[$name] );


        
    }
    
}