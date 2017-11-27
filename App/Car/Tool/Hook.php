<?php
namespace App\Car\Tool;
use App\Car\Model\ErrorApiModel;
use App\Car\Model\SuccessApiModel;

class Hook implements \Lib\Tool\Hook{

    # ajax 回调
    static function ajaxCallback($code = 0,$data = [],$message = '',$url = ''){

        
        try{
            if($code!=200){
                $req = json_encode($_REQUEST);
                ErrorApiModel::copyMutiInstance()->set([
                    'request'=>$req,'output'=>$message,'date'=>date('Y-m-d H:i:s'),'path'=>REQUEST_PATH
                ])->add();

            }elseif(Config::get('SUCCESS_LOG')){
                $req = json_encode($_REQUEST);
                $data = json_encode($data);
                SuccessApiModel::copyMutiInstance()->set([
                    'request'=>$req,'output'=>$data,'date'=>date('Y-m-d H:i:s'),'path'=>REQUEST_PATH
                ])->add();

            }
            

        }catch(\Exception $e){

        }catch(\Error $e){

        }


    }





}