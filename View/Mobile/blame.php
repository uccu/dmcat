<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta name="apple-themes-web-app-capable" content="yes">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="format-detection" content="telephone=no">
    <title>社会责任</title>
    <link rel="stylesheet" href="css/swiper.min.css">
    <link href="css/reset.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script src="js/swiper.jquery.min.js"></script>
</head>
<style>
    .news-box ul li h1,.news-box ul li h3{padding: 0}
    .news-box ul li h3{font-size:10px}
</style>
<body>
<header>
    <div class="header-left" onclick="getBackShuaXin()"></div>
    <div class="header-center"></div>
    <div class="header-right"></div>
</header>
<p class="fenge"></p>
<div class="group-banner">
    <img src="img/group/sbanner.png">
</div>
<div class="response-top">
    <p>德之关爱 <em>做德的使者，传递人间温暖</em></p>
    <span>山与山的相遇： 从灵秀的雁荡山，到雄浑的天山，纵跨八千里江山的世纪交汇。东岸与西域的碰撞：高原景象诸如江南神韵，点亮商业世界的全</span>
</div>
<div class="chairman-search">
    <!--input type="search" placeholder="输入关键字"-->
</div>
<div class="news-box cultural-title">
    <ul>
        <li>
            <h1 class="news-active">社会责任</h1>
            <h3>SOCIAL RESPONSIBILITY</h3>
        </li>
        
    </ul>
    <div class="response-list">
        <h1>德汇慈善事业</h1>
        <?php
                        foreach($charitable as $v){

                            echo '<div class="response-listbox">
            <img src="/pic/'.$v->pic.'">
            <div class="resonse-listtext">
                <h2>'.$v->year.'年'.$v->month.'月</h2>
                <p>'.langV($v,'description').'</p>
            </div>
        </div>';
                        }
                    
                    ?>
        
        

    </div>
</div>
<?php include('side_slider.php');?>
</body>
</html>