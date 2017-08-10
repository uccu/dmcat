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
    <title>发展历程</title>
    <link rel="stylesheet" href="css/swiper.min.css">
    <link href="css/reset.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script src="js/swiper.jquery.min.js"></script>
</head>
<body>
<header>
    <div class="header-left" onclick="getBackShuaXin()"></div>
    <div class="header-center"></div>
    <div class="header-right"></div>
</header>
<p class="fenge"></p>
<div class="group-banner">
    <img src="img/group/banner1.png">
</div>
<div class="chairman-search">
    <!--input type="search" placeholder="输入关键字"-->
</div>
<div class="main-box" style="margin-bototm:0">
    <h1>发展历程</h1>
    <h3>DEVELOPMENT HISTORY</h3>
    <div class="develop-nav">
        <a href="javascript:void(0);" class="develop-nav-left" onclick="navMove(1);"></a>
        <div class="develop-nav-box">
            <div id="develop_box" style="width: 1344px; margin-left: 0px;">
                        <?php 
                            
                            foreach($years as $i){
                                if($year == $i || (!$year && $i == $yearM))echo '<span class="checked">'.$i.'</span>';
                                else echo '<span>'.$i.'</span>';
                            }
                        ?>
            </div>
        </div>
        <a href="javascript:void(0);" class="develop-nav-right" onclick="navMove(-1);"></a>
    </div>
    
</div>
<div class="develop-content development-history">
                    <?php

                        foreach($list as $v){
                            echo '<div class="develop-one-month">
                            <span>'.$v->month.'月</span>
                            <div>
                                <h1>'.langV($v,'title').'</h1>
                                <h2>'.langV($v,'description').'</h2>
                            </div></div>';

                        }

                    ?>
</div>
<script type="text/javascript">
    var obj_width = 84;
    var num = $("#develop_box > span").length;
    $("#develop_box").css("width", num * obj_width + "px");
    var no_move = true;

    $(document).ready(function () {
        var checkedIndex = $('.checked').index()
        $("#develop_box").css("margin-left", -(checkedIndex * $('#develop_box > span').width()) + "px");

        $("#develop_box > span").click(function () {
            var obj = $(this);
            location = location.pathname+'?year='+obj.text();        
        });


    });

    function navMove(type) {
        if (!no_move) {
            return false;
        }
        no_move = false;
        setTimeout(function () {
            no_move = true;
        }, 400);
        type = type == 1 ? 1 : -1;
        var obj = $("#develop_box");
        var left = parseInt(obj.css("margin-left"));
        if (type == -1 && left <= -(num - 10) * obj_width) {
            return false;
        }
        if (type == 1 && left >= 0) {
            return false;
        }
        obj.css("margin-left", left + (2.9*obj_width * type) + "px");
    }
</script>
<?php include('side_slider.php');?>
</body>
</html>