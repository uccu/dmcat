<?php

namespace App\Blog\Model;
use Model;

class ConfigModel extends Model{

    public $table = 'config';
    public $primary = 'key';

    public function getConfig($key){
        $data = $this->find($key)->content;
        return json_decode($data);
    }
    public function getConfigVal($key){
        return $this->find($key)->content;
    }
}