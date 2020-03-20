<?php

namespace App\Resource\Model;
use Model;


class SiteResourceModel extends Model{

    public $table = 'site_resource';

    public $field = ['id','site_id','resource_id','outlink'];


    public function site(){

        return $this->join(SiteModel::class,'id','site_id');
        
    }

    
}