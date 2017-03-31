<?php

namespace App\School\Model;
use Model;

class I18nModel extends Model{

    public $table = 'i18n';


    /* 获取列表 */
    public function getter($type,$language){

        return $this->where(
            ['type'=>$type]
        )->get_field($language,'name');
    }

    

    

}