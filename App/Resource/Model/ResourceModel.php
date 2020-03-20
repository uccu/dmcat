<?php

namespace App\Resource\Model;
use Model;

class ResourceModel extends Model{

    public $table = 'resource';

    public $field = ['id','theme_id','subtitle_id',
    'name','content','resource_type_id','download','down_type_id',
    'hash','base32','md5','size','password','visible','show_times',
    'down_times','level','ctime','user_id','additional','tags','unftags','new_number'];


    public function sitelink(){

        return $this->join(SiteResourceModel::class,'resource_id','id');
        
    }

    public function theme(){

        return $this->join(ThemeModel::class,'id','theme_id');
        
    }

    public function findHash($hash){

        return $this->where(['hash'=>$hash])->find();
    }

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
        
    }

    

}