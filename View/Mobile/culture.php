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
<!--div class="group-banner">
    <img src="img/group/culturalbanner.png">
</div>
<div class="chairman-search">
    <input type="search" placeholder="输入关键字">
</div-->
<div class="news-box cultural-title">
    <ul>
        
        <li>
            <h1 class="news-active">文化体系</h1>
            <h3>CULTURAL SYSTEM</h3>
        </li>
        
    </ul>
    <div style="padding:5px">
        <?php echo langV($page,'content');?>
    </div>
</div>
<?php include('side_slider.php');?>

</body>
</html>