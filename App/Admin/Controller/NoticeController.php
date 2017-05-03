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


    /* 不需要回复 */
    function notice(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 需要回复 */
    function notice_r(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 回执 */
    function notice_v(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 投票 */
    function notice_t(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    
    
    


}