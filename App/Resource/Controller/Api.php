<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;
use stdClass;

class Api extends Controller{


    function __construct(){

        

    }

    function add(Request $request){

        $info = new stdClass;

        $token = $request->request('token');

        $info->name     = $request->request('name');
        $info->outlink  = $request->download('outlink');
        $info->hash     = $request->download('hash');



    }

    function delete(){
        


    }

    function update(){



    }



}