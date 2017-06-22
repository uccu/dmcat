<?php

namespace App\Doowin\Controller;


use Controller;
use App\Doowin\Model\UploadModel;

class DownloadController extends Controller{


    function __construct(){

        $this->salt = $this->L->config->site_salt;

    }

    function file($id = 0,UploadModel $model){

        $file = $model->find($id);
        !$file && Response::getInstance()->r302('/404.html');

        $path = BASE_ROOT.'upload/'.$file->path;
        !is_file($path) && Response::getInstance()->r302('/404.html');

        $fp = fopen($path,"r");
        $file_size = filesize($path);

        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length:".$file_size);
        header("Content-Disposition: attachment; filename=".$file->name);
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
