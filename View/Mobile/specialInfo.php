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
    <title>新闻中心</title>
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
<!--div class="choose">
    <select name="" class="slect-year">
        <option value="">选择年份</option>
        <option value="">1985</option>
        <option value="">1986</option>
    </select>
    <input type="search" class="seach" placeholder="输入关键字">
</div-->
<div class="news-detail">
    <div class="news-detail-title">
        <h1><?php echo langV($info,'title');?></h1>
        <span>发布时间： <?php echo date('Y-m-d',$info->create_time);?></span>
        <span>浏览次数：<?php echo $info->browse;?></span>
    </div>
    <div>
        <style>
            img{height:auto !important}
        </style>
        <?php echo langV($info,'content');?>
    </div>
    
</div>
<div class="news-detail-share" style="padding-top:30px">
    <!--div class="prevNext">
        <span>上一篇: </span><span href="#">暂无内容</span>
    </div>
    <div class="prevNext">
        <span>下一篇: </span><a href="#">市工商局领导莅临德汇指导党建工作</a>
    </div-->
    <div class="share">
        <!-- JiaThis Button BEGIN -->
        <div class="jiathis_style">
            <a class="jiathis_button_qzone"></a>
            <a class="jiathis_button_tsina"></a>
            <a class="jiathis_button_tqq"></a>
            <a class="jiathis_button_weixin"></a>
            <a class="jiathis_button_renren"></a>
            <a href="http://www.jiathis.com/share" class="jiathis jiathis_txt jtico jtico_jiathis" ></a>
            <a class="jiathis_counter_style"></a>
        </div>
        <script type="text/javascript" src="http://v3.jiathis.com/code_mini/jia.js" charset="utf-8"></script>
        <!-- JiaThis Button END -->
    </div>

</div>
<?php include('side_slider.php');?>
</body>
</html>