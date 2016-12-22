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

        $data['list'] = $themeModel->where($condition)->page($page,$limit)->order('ctime',$desc?1:0)->get();

        AJAX::success($data);

    }

    function week(ThemeModel $themeModel){

        
        $all = $themeModel->where('%F > %d','change_time',TIME_NOW-3600*24*7*2)->order('change_time')->get();

        $today = $last_week = $this_week = [];



        foreach($all as $theme){

            $theme->date = Func::time_calculate($theme->change_time);

            if($theme->change_time<TIME_NOW-3600*24*7){

                $last_week[date('w',$theme->change_time)][] = $theme;


            }elseif($theme->change_time<TIME_NOW-3600*24){

                $this_week[date('w',$theme->change_time)][] = $theme;
            }else{

                $today[] = $theme;
            }

            

        }
        $data['today'] = $today;
        $data['this_week'] = $this_week;
        $data['last_week'] = $last_week;

        AJAX::success($data);



    }

    function hour24(ThemeModel $themeModel){


        $all = $themeModel->where('%F > %d','change_time',TIME_NOW-3600*24)->order('change_time')->get();

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