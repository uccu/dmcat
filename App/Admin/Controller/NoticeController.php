<?php

namespace App\Admin\Controller;

use View;
use Request;
use Controller;
use App\School\Middleware\L;

class NoticeController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

    }


    /* 通知 */
    function notice(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    function notice_confirm(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->school;
        $lang->classes;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 投票 */
    function vote(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 活动 */
    function activity(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 校外宣传 */
    function propaganda(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    
    
    


}