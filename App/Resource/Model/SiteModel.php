<?php

namespace App\Resource\Model;
use Model;

class SiteModel extends Model{

    public $table = 'site';

    protected $field = ['id','name','token'];

    public function findToken($token){

        if(!$token)return NULL;
        return $this->where(['token'=>$token])->find();
    }
    

    
}