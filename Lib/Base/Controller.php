<?php

use Lib\Sharp\SingleInstance;

class Controller implements SingleInstance{

    
    public static function getInstance(){

        return table(get_called_class());

    }


    
}