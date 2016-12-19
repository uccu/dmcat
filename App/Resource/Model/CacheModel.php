<?php

namespace App\Resource\Model;
use Model;

class CacheModel extends Model{

    public $table = 'cache';

    protected $field = ['id','name','content'];

    function cget($name){
        $this->clean();
        if(is_numeric($name))return ($r = $this->find($name))?$r->content:null;
        else return ($r = $this->where('name=%n',$name)->find())?$r->content:null;
    }

    function cadd($name ,$content,$replace = false){
        
        return $this->clean()->set(['name'=>$name,'content'=>$content])->add($replace);
    }
    function csave($name ,$content){
        $this->clean();
        if(is_numeric($name))return $this->set(['content'=>$content])->save($name);
        else return $this->where(['name'=>$name])->set(['content'=>$content])->save();
    }
    function cremove($name){

        $this->clean();
        if(is_numeric($name))return $this->remove($name);
        else return $this->where(['name'=>$name])->remove();
    }
    
}