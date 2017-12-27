<?php

namespace App\Admin\Set;

use Uccu\DmcatTool\Tool\AJAX;
use App\Car\Tool\Func;
use App\Car\Middleware\L3;
use Uccu\DmcatTool\Traits\InstanceTrait;

class Lists{

    use InstanceTrait;

    public $opt = [];
    public $tbody = [];
    public $thead = [];
    public $list = [];
    public $page = 1;
    public $limit = 1;
    public $max = 1;
    public $where = [];
    public $model;
    public $name = '';

    public function __construct($model,$page,$limit){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

        !$model->field && AJAX::error('字段没有公有化！');
        $this->model = $model;
        $this->page = $page?$page:1;
        $this->limit = $limit?$limit:1;

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
    

    public function setOptReq($a = ''){
        $this->opt['req'][] = $a;
        return count($this->opt['req']) - 1;
    }

    public function setBody($a = ''){
        $this->tbody[] = $a;
    }

    public function setHead($a = ''){
        $this->thead[] = $a;
    }


    public function showAll(){

        
    }

    public function getList($all = 0){

        if($this->list)return $this->list;

        
        if(!$all)$this->model->page($this->page,$this->limit);
        else{
            $this->page = 1;
            $this->max = count($this->list);
            $this->limit = count($this->list);
        }
        return $this->list = $this->model->where($this->where)->get()->toArray();
    }


    public function output(){

        # 分页内容
        $this->max    = $this->model->where($this->where)->select('COUNT(*) AS c','RAW')->find()->c;

        $out = 
        [

            'opt'   =>  $this->opt,
            'thead' =>  $this->thead,
            'tbody' =>  $this->tbody,
            'list'  =>  $this->list,
            'page'  =>  $this->page,
            'limit' =>  $this->limit,
            'max'   =>  $this->max,
            'name'  =>  $this->name,
        
        ];

        AJAX::success($out);
    }


    public function fullPicAddr($name = ''){

        foreach($this->list as &$v){
            $v->$name = Func::fullPicAddr($v->$name);
        }

    }

    public function fetchArr($name = '',$k = '',$arr = []){

        foreach($this->list as &$v){
            $v->$name = $arr[$v->$k];
        }

    }


    public function each($fun){

        foreach($this->list as &$v){
            $fun($v);
        }

    }




}