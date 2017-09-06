<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Lawyer\Middleware\L;

class ChatController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();
        

    }

    /* 快速问题 */
    function fast(){
        
        View::addData(['getList'=>'/chat/admin_fast']);
        View::hamlReader('home/list','Admin');
    }
    
    function send(){

        View::addData(['getList'=>'/chat/admin_send']);
        View::hamlReader('chat/send','Admin');
    }

    function user_chat($id){

        View::addData(['getList'=>'/chat/admin_user_chat?id='.$id]);
        View::hamlReader('home/list','Admin');

    }

    function index($id){

        View::hamlReader('chat/index','Admin');

    }

    function user_chats($lawyer_id,$user_id){
        
        View::addData(['getList'=>'/chat/admin_user_chats?lawyer_id='.$lawyer_id.'&user_id='.$user_id]);
        View::hamlReader('home/list','Admin');

    }

    

    


}