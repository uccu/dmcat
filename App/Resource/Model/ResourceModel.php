<?php

namespace App\Resource\Model;
use Model;

class ResourceModel extends Model{

    public $table = 'resource';

    protected $field = ['id','theme_id','subtitle_id',
    'name','content','resource_type_id','download','down_type_id',
    'hash','base32','md5','size','password','visible','show_times',
    'down_times','level','ctime'];


    public function sitelink(){

        return $this->join(SiteResourceModel::class);
        
    }

    public function theme(){

        return $this->join(ThemeModel::class,'id','theme_id');
        
    }

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
        
    }

    

}