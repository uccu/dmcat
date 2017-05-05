<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;

use View;
use App\Resource\Model\ThemeModel;
use App\Resource\Tool\Func;
use fengqi\Hanzi\Hanzi;

class ThemeController extends Controller{


    function __construct(){

        

    }

    function all($p,$page = 1,$limit = 20,$season = null,$desc = 1,ThemeModel $themeModel,$search,$sort = 'id'){

        if($p != '4moe')AJAX::error('权限不足');

        $condition = [];

        if($season)$condition['season'] = $season;

        if($search){

            $search = str_replace('%','',trim($search));
            $condition['search'] = ['%F LIKE %n','name','%'.$search.'%'];
        }
        

        $data['list'] = $themeModel
                            ->where($condition)
                            ->page($page,$limit)
                            ->order($sort,$desc?1:0)
                            ->get()->toArray();

        $gdata['title'] = '主题列表';


        View::addData($gdata);

        View::hamlReader('Theme/all','Resource',$data);

    }

    


    function week2(ThemeModel $themeModel){

        $all = $themeModel->where('%F > %d AND visible = 1','change_time',TIME_TODAY-3600*24*7*2)->order('change_time','DESC')->get()->toArray();

        $week = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六',];
        $week2 = ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日',];



        foreach($all as &$theme){
            
            $theme->date = Func::time_calculate($theme->change_time);

            $theme->week = $week[date('w',$theme->change_time)].'/'.$week2[date('w',$theme->change_time)];


        }

        View::addData(['all'=>$all]);

        

        View::hamlReader('Theme/week2','Resource');




    }

    function hour24(ThemeModel $themeModel){


        $all = $themeModel->where('%F > %d AND visible = 1','change_time',TIME_NOW-3600*24)->order('change_time')->get();

        $today = $yesterday = [];



        foreach($all as $theme){

            $theme->date = Func::time_calculate($theme->change_time);

            if($theme->change_time<TIME_TODAY){

                $yesterday[] = $theme;
            }else{

                $today[] = $theme;
            }

            

        }
        $data['today'] = $today;
        $data['yesterday'] = $yesterday;

        

        AJAX::success($data);

    }



}