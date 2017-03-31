<?php
namespace App\School\Middleware;
use Middleware;

use App\School\Model\I18nModel;

class I18n extends Middleware{

    public $language = 'cn';

    function setLanguage($l){

        return $this->languge = $l;
    }

    
    function __get($name){

        $this->$name = I18nModel::getInstance()->getter($name,$this->language);

        return $this->$name;
    }
    
    
}