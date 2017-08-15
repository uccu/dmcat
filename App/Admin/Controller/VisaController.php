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


    function work(){

        View::addData(['getList'=>'/visa/admin_visa_work']);
        View::hamlReader('home/list','Admin');
    }

    function family(){

        View::addData(['getList'=>'/visa/admin_visa_family']);
        View::hamlReader('home/list','Admin');
    }

    function refuse(){

        View::addData(['getList'=>'/visa/admin_visa_refuse']);
        View::hamlReader('home/list','Admin');
    }

    function travel(){

        View::addData(['getList'=>'/visa/admin_visa_travel']);
        View::hamlReader('home/list','Admin');
    }

    function marry(){

        View::addData(['getList'=>'/visa/admin_visa_marry']);
        View::hamlReader('home/list','Admin');
    }
    
    function graduate(){

        View::addData(['getList'=>'/visa/admin_visa_graduate']);
        View::hamlReader('home/list','Admin');
    }
    
    function student(){

        View::addData(['getList'=>'/visa/admin_visa_student']);
        View::hamlReader('home/list','Admin');
    }
    
    function perpetual(){

        View::addData(['getList'=>'/visa/admin_visa_perpetual']);
        View::hamlReader('home/list','Admin');
    }

    function technology(){

        View::addData(['getList'=>'/visa/admin_visa_technology']);
        View::hamlReader('home/list','Admin');
    }
    
    function business(){

        View::addData(['getList'=>'/visa/admin_visa_business']);
        View::hamlReader('home/list','Admin');
    }

}