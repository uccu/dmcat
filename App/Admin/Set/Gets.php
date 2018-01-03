<?php

namespace App\Admin\Set;

use Uccu\DmcatTool\Tool\AJAX;
use App\Car\Tool\AdminFunc;
use App\Car\Middleware\L3;
use Uccu\DmcatTool\Traits\InstanceTrait;

class Gets{

    use InstanceTrait;

    public $opt = [];
    public $info = false;
    public $model;
    public $tbody;
    public $id;

    public $name = '';

    public function __construct($model,$id){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

        !$model->field && AJAX::error('字段没有公有化！');
        $this->model = $model;
        $this->id = $id;

    }

    public function checkPermission($n){

        $this->L->adminPermissionCheck($n);
    }

    # 设置名字
    public function setName($n = ''){
        $this->name = $n;
    }
    public function setOpt($n = '',$a = ''){
        $this->opt[$n] = $a;
    }
    

    public function setOptReq($n = '',$a = ''){
        $this->opt['req'][] = $a;
        return count($this->opt['req']) - 1;
    }

    public function setBody($a = ''){
        $this->tbody[] = $a;
        return count($this->tbody) - 1;
    }

    public function getInfo(){
        if($this->info)return $this->info;
        return $this->info = AdminFunc::get($this->model,$this->id);
    }




    public function output(){

        if(!$this->info)$this->info = AdminFunc::get($this->model,$this->id);

        $out = [
            'info'  =>  $this->info,
            'tbody' =>  $this->tbody,
            'name'  =>  $this->name,
            'opt'   =>  $this->opt,
        ];

        AJAX::success($out);
    }


    public function fetchArr($name = '',$k = '',$arr = []){

        $this->info->$name = $arr[$this->info->$k];

    }









}