<?php

namespace App\Admin\Controller;

use Controller;
use View;


class VisaController extends Controller{


    function __construct(){

        
    }

    function setting($type){

        View::addData(['getList'=>'/visa/admin_visa_setting?type='.$type]);
        View::hamlReader('home/list','Admin');
    }

    function setting_option($id){

        View::addData(['getList'=>'/visa/admin_visa_setting_option?id='.$id]);
        View::hamlReader('home/list','Admin');
    }
    

}