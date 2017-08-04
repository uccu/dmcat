<?php

namespace App\Lawyer\Model;

use Model;


class UserConsultLimitModel extends Model{

    public $table = 'user_consult_limit';

    public function rule(){

        return $this->join(ConsultPayRuleModel::class,'id','rule_id');
    }
}