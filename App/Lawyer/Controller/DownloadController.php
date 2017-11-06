<?php

namespace App\Lawyer\Controller;


use Controller;
use Response;
use Request;
use View;
use App\Lawyer\Model\UploadModel;

class DownloadController extends Controller{


    function __construct(){

        $this->salt = $this->L->config->site_salt;

    }

    function file($id = 0,UploadModel $model){

        if(!$id)$id = Request::getSingleInstance()->folder[2];

        $file = $model->find($id);
        !$file && Response::getSingleInstance()->r302('/404.html');

        $path = BASE_ROOT.'upload/'.$file->path;
        !is_file($path) && Response::getSingleInstance()->r302('/404.html');

        $fp = fopen($path,"r");
        $file_size = filesize($path);

        header('Content-Type: application/octet-stream');
        header("Accept-Ranges: bytes");
        header("Accept-Length:".$file_size);
        header("Content-Disposition: attachment;filename=".$file->name);
        $buffer = 1024;
        $file_count = 0;

        while(!feof($fp) && $file_count < $file_size){
            $file_con = fread($fp,$buffer);
            $file_count += $buffer;
            echo $file_con;
        } 
        fclose($fp);


    }

    

    
    


}
