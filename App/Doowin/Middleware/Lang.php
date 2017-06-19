<?php

    use App\Doowin\Middleware\L;

    function langV($v,$name,$n = 0){
        if(!$v)return '';
        $name_en = $name.'_en';
        if(L::getInstance()->lang == 'en' && $v->$name_en){

            if($n && mb_strlen($v->$name_en) > 2 * $n){
                return mb_substr($v->$name_en,0,2 * $n).'...';
            }
            return $v->$name_en;
        }
        if($n && mb_strlen($v->$name) > $n){
                return mb_substr($v->$name,0, $n).'...';
            }
        return $v->$name;
    }

    function lang($name){

        $array = [
            '德汇集团旗下网站'=>'Doowin Group\'s Web Site',
            '德汇集团'=>'Doowin Group',
            '德汇教育'=>'Doowin Education',
            '德汇金融'=>'Doowin Financial',
            '输入关键字'=>'Input Keywords',
            '加入我们'=>'Join Us',
            '联系我们'=>'Contact Us',
            '联系电话'=>'Contact Number',
            '关注我们'=>'Concern Us',
            '传真'=>'Fax',
            '地址'=>'Address',


            '首页'=>'Home',
            '走进德汇'=>'Enter Doowin',
            '集团简介'=>'Group Profile',
            '董事长专区'=>'President Zone',
            '发展历程'=>'Development History',
            '企业文化'=>'Corporate Culture',
            '企业荣誉'=>'Enterprise hHonor',
            '社会责任'=>'Social Responsibility',

            '德汇产业'=>'Doowin Industry',
            '德汇新天地'=>'World',
            '万达广场'=>'Wanda Plaza',
            '亚欧新城'=>'New Town',
            '新闻中心'=>'News Center',
            '集团要闻'=>'Group News',
            '热点专题'=>'Hot Topics',
            '媒体聚焦'=>'Media Focus',
            '视频中心'=>'Video Center',
            '德汇招聘'=>'Dorsey Recruitment',
            '招式公告'=>'Style Announcement',
            '投诉及建议'=>'Complaints&Suggestions',
            '法律声明'=>'Legal Statement',

            '查看更多'=>'MORE',


            '董事长图片'=>'President\'s Picture',

        ];
        if(L::getInstance()->lang == 'en' && $array[$name]){

            return $array[$name];
        }
        return $name;

    }

