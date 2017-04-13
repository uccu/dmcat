<?php

namespace App\School\Controller;


use App\School\Model\CourseModel;

use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;
use App\School\Tool\Func;

class CourseController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 列表 */
    function lists(CourseModel $model,$classes_id = 0){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/course/get','upd'=>'/course/upd','del'=>'/course/del'];

        $out['thead'] = [
            ''=>['class'=>'tc'],
            'Mon'=>['class'=>'tc'],
            'Tue'=>['class'=>'tc'],
            'Wed'=>['class'=>'tc'],
            'Thu'=>['class'=>'tc'],
            'Fri'=>['class'=>'tc'],
            'Sat'=>['class'=>'tc'],
            'Sun'=>['class'=>'tc'],
        ];

        $out['tbody'] = [
            0=>['class'=>'tc'],
            1=>['class'=>'tc changeIt cp t'],
            2=>['class'=>'tc changeIt cp t'],
            3=>['class'=>'tc changeIt cp t'],
            4=>['class'=>'tc changeIt cp t'],
            5=>['class'=>'tc changeIt cp t'],
            6=>['class'=>'tc changeIt cp t'],
            7=>['class'=>'tc changeIt cp t'],
        ];

        
        
        $out['lang'] = $this->lang->language;

        $where['classes_id'] = $classes_id;
        $list = $model->where($where)->get()->toArray();

        $listw = [];
        
        for($i = 1;$i<=8;$i++){
            $listw[$i][0] = $i;
            for($j = 1;$j<=7;$j++){
                $listw[$i][$j] = '';
                foreach($list as $v){
                    if($v->step == $i && $v->week == $j){
                        $listw[$i][$j] = $v->name;break;
                    }
                }
            }
        }


        $out['list']  = $listw;
        AJAX::success($out);


    }

    function get($classes_id,$week,$step,CourseModel $model){

        !$classes_id && AJAX::error_i18n('no_data');
        $where['classes_id'] = $classes_id;
        $where['week'] = $week;
        $where['step'] = $step;
        $out['info'] = $info = $model->where($where)->find($id);
        !$info && AJAX::success(['info'=>$where]);
        $out['lang'] = $this->lang->language;

        AJAX::success($out);

    }

    function upd($classes_id,$week,$step,CourseModel $model,$name){

        $data = Request::getInstance()->request($model->field);
        

        $where['classes_id'] = $classes_id;
        $where['week'] = $week;
        $where['step'] = $step;

        $info = $model->where($where)->find();

        if(!$name){
            $model->where($where)->remove();
        }elseif(!$info){

            $data['create_time'] = TIME_NOW;
            $id = $model->set($data)->add()->getStatus();

        }else{

            unset ($data['classes_id']);
            unset ($data['week']);
            unset ($data['step']);



            $model->set($data)->where($where)->save();
        }

        
        
        

        AJAX::success();

    }


    


    


}
