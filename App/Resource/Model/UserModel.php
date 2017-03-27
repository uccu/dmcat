<?php

namespace App\Resource\Model;
use Model;

class UserModel extends Model{

    public $table = 'user';

    protected $field = ['id','nickname','email','password','salt','type','score','ctime','token'];

    

    
}