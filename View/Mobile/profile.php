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
    <title>集团简介</title>
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
    <img src="img/group/banner.png">
</div>
<div class="choose">
    <select name="" class="slect-year">
        <option value="">选择年份</option>
        <option value="">1985</option>
        <option value="">1986</option>
    </select>
    <input type="search" class="seach" placeholder="输入关键字">
</div>
<div class="main-box">
    <h1>集团简介</h1>
    <h3>GROUP PROFILE</h3>
    <div class="group-text">
        <?php echo langV($page,'content');?>
    </div>
</div>
<div class="main-box">
    <h1>德汇产品线</h1>
    <h3>PRODUCT LINE</h3>
    <ul class="productline">
        <?php
                foreach($introductionProduct as $v){

                    echo '<li>
            <h5>'.langV($v,'title').'</h5>
            <div class="productline-detail">
                <img src="/pic/'.$v->pic.'" alt="">
                '.langV($v,'content').'
            </div>
        </li>';
                }
            
            ?>
        
        
    </ul>
</div>
<?php include('side_slider.php');?>
<script>
    $('.productline').find('h5').click(function(){
        var that = $(this).parent();
        that.find('.productline-detail').toggle('100',function(){
            var obj = $(this);
            if (obj.css("display") == "none") {
                obj.siblings("h5").css({'background':'url("img/group/bottom.png") no-repeat','background-position':'5.3rem center','background-size':'0.3rem'});
            } else {
                obj.siblings("h5").css({'background':'url("img/group/sanjiao1.png") no-repeat','background-position':'5.3rem center','background-size':'0.3rem'});

            }
        });

    })
</script>
</body>
</html>