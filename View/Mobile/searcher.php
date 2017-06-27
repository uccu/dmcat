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
    <title>董事长专区</title>
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
<form class="chairman-search" id="search" method="post" onsubmit="return false;">
    <select name="type">
            <option value="1"><?php echo lang('集团要闻');?></option>
            <option value="2"><?php echo lang('热点专题');?></option>
            <option value="3"><?php echo lang('媒体聚焦');?></option>
            <option value="4"><?php echo lang('视频中心');?></option>
        </select>
    <input type="search" placeholder="输入关键字">
</form>

    <script>
        $('form#search').submit(function(){
            $.post('/news/search',$(this).serialize(),function(d){
                if(d.url)location = d.url
            },'json')

        })
    </script>
<?php include('side_slider.php');?>

</body>
</html>