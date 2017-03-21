<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;

use View;
use App\Resource\Model\ThemeModel;
use App\Resource\Tool\Func;

class ThemeController extends Controller{


    function __construct(){

        

    }

    function all($page = 1,$limit = 20,$season = null,$desc = 1,ThemeModel $themeModel){

        $condition = [];

        if($season)$condition['season'] = $season;

        $data['list'] = $themeModel->where($condition)->page($page,$limit)->order('ctime',$desc?1:0)->get()->toArray();

        $gdata['g']['title'] = '主题列表';

        AJAX::success($data);

        // View::addData($gdata);

        // View::hamlReader('Theme/all','Resource',$data);

    }

    function week(ThemeModel $themeModel){

        
        $all = $themeModel->where('%F > %d AND visible = 1','change_time',TIME_TODAY-3600*24*7*2)->order('change_time')->get()->toArray();

        $today = $last_week = $this_week = [];



        foreach($all as $theme){

            $theme->date = Func::time_calculate($theme->change_time);

            if($theme->change_time<TIME_TODAY-3600*24*7){

                $last_week[date('w',$theme->change_time)][] = $theme;


            }elseif($theme->change_time<TIME_TODAY){

                $this_week[date('w',$theme->change_time)][] = $theme;
            }else{

                $today[] = $theme;
            }

            

        }
        $data['today'] = $today;
        $data['this_week'] = $this_week;
        $data['last_week'] = $last_week;

        $data['test'] = '1';

        //var_dump($this_week);

        $gdata['g']['title'] = '节目单';

        //AJAX::success($data);

        View::addData($gdata);

        View::hamlReader('Theme/week','Resource',$data);



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