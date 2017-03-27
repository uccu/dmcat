<?php

namespace App\School\Controller;



use Controller;

class GitController extends Controller{


    function __construct(){

        

    }


    /* 通过用GIT进行代码同步 */
    function pull(){

        system("cd ".BASE_ROOT." && \"C:\Program Files\Git\git-cmd.exe\" git pull");


    }


    


}