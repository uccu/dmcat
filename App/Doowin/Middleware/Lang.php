<?php

    use App\Doowin\Middleware\L;

    function langV($v,$name,$n = 0){
        if(!$v)return '';
        $name_en = $name.'_en';
        if(L::getInstance()->lang == 'en' && $v->$name_en){

            if($n && mb_strlen($v->$name_en) > 2 * $n){
                return mb_substr($v->$name_en,0,2 * $n).'...';
            }
            str_replace("\n",'<br>',$v->$name_en);
            return $v->$name_en;
        }
        if($n && mb_strlen($v->$name) > $n){
                return mb_substr($v->$name,0, $n).'...';
            }
        str_replace("\n",'<br>',$v->$name);
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
            '选择年份'=>'Choose Year',

            '上一页'=>'Prev',
            '下一页'=>'Next',
            '末页'=>'Last',
            '每页显示'=>'Each page shows ',
            '条，共'=>' records, with ',
            '条记录'=>' records',

            '首页'=>'Home',
            '走进德汇'=>'Enter Doowin',
            '集团简介'=>'Group Profile',
            '董事长专区'=>'President Zone',
            '发展历程'=>'Development History',
            '企业文化'=>'Corporate Culture',
            '企业荣誉'=>'Enterprise Honor',
            '社会责任'=>'Social Responsibility',

            '德汇宝贝广场'=>'Baby Plaza',
            '德汇万达广场'=>'Wanda Plaza',
            '德汇特色小镇'=>'Characteristic Town',
            '德汇物流'=>'Logistics',
            '德汇刊报'=>'Doowin Newspaper',

            '德汇产业'=>'Doowin Industry',
            '德汇新天地'=>'New World',
            '万达广场'=>'Wanda Plaza',
            '亚欧新城'=>'New Town',
            '新闻中心'=>'News Center',
            '集团要闻'=>'Group News',
            '热点专题'=>'Hot Topics',
            '媒体聚焦'=>'Media Focus',
            '视频中心'=>'Video Center',
            '德汇招聘'=>'Dorsey Recruitment',
            '招标公告'=>'Bid announcement',
            '投诉及建议'=>'Complaint&Suggestion',
            '法律声明'=>'Legal Statement',

            '查看更多'=>'MORE',
            '发布时间'=>'Publish Date',
            '了解其它相关视频'=>'Other Video',

            '董事长图片'=>'President\'s Picture',
            '德汇企业形象广告画册'=>'Doowin Corporate advertising album',
            '德汇企业宣传画册'=>'Doowin Enterprise Brochure',
            '德汇运营创新“产学研一体”画册'=>'"research in one" album',

        ];
        if(L::getInstance()->lang == 'en' && $array[$name]){

            return $array[$name];
        }
        return $name;

    }

