<?php

namespace App\Doowin\Controller;


use Controller;
use Response;
use Request;
use View;
use App\Doowin\Model\UploadModel;

class DownloadController extends Controller{


    function __construct(){

        $this->salt = $this->L->config->site_salt;

    }

    function file($id = 0,UploadModel $model){

        if(!$id)echo $id = Request::getInstance()->folder[2];

        $file = $model->find($id);
        !$file && Response::getInstance()->r302('/404.html');

        $path = BASE_ROOT.'upload/'.$file->path;
        !is_file($path) && Response::getInstance()->r302('/404.html');

        $fp = fopen($path,"r");
        $file_size = filesize($path);

        header("Content-Type:application/pdf");
        header("Accept-Ranges: bytes");
        header("Accept-Length:".$file_size);
        header("filename=".$file->name);
        $buffer = 1024;
        $file_count = 0;

        while(!feof($fp) && $file_count < $file_size){
            $file_con = fread($fp,$buffer);
            $file_count += $buffer;
            echo $file_con;
        } 
        fclose($fp);


    }

    function watch($id,UploadModel $model){

        $file = $model->find($id);
        !$file && Response::getInstance()->r302('/404.html');

        $path = BASE_ROOT.'upload/'.$file->path;
        !is_file($path) && Response::getInstance()->r302('/404.html');

        View::addData(['path'=>'/download/file/'.$id.'.pdf']);


        View::hamlReader('watch','App');
    }

    function upload(){
    //insert into dw_news_group (title,description,content,create_time,pic,browse) select info_title as title,info_s_content as description, info_content,UNIX_TIMESTAMP(info_time) as create_time,info_picture as pic,info_hit as browse from dw_info where channel_id = 72
        header('Location:http://f.hualip.com/'.REQUEST_PATH);
    }
    


}
