<?php
namespace App\App\Tool;
use App\App\Model\ErrorApiModel;

class Hook implements \Lib\Tool\Hook{

    # ajax 回调
    static function ajaxCallback($code = 0,$data = [],$message = '',$url = ''){

        if($code!=200)
        try{

            $req = json_encode($_REQUEST);
            ErrorApiModel::copyMutiInstance()->set([
                'request'=>$req,'output'=>$message,'date'=>date('Y-m-d H:i:s'),'path'=>REQUEST_PATH
            ])->add();

        }catch(\Exception $e){

        }catch(\Error $e){

        }


    }





}