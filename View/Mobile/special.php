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
<div class="main-box" style="margin-bottom: 0">
    <h1>热点专题</h1>
    <h3>HOT TOPIC</h3>
</div>
<div class="develop-content hottopic-content">

    <?php foreach($list as $info){?>
                
    <div class="develop-one-month">
        <span>
            <?php echo date('m-d',$info->create_time);?>
            <em><?php echo date('Y',$info->create_time);?></em>
        </span>
        <div class="develop-one-monthtitle">
            <div class="develop-one-monthimg">
                <img src="/pic/<?php echo $info->pic;?>" >
                <a href="specialInfo?id=<?php echo $info->id;?>"><h1><?php echo langV($info,'title');?></h1></a>
            </div>
            <h2><?php echo langV($info,'description',150);?></h2>
        </div>
    </div>
    <?php }?>
    <style>
        a{color:inherit}
        #page_content {height: 24px; margin-top: 30px; line-height: 24px;}
        #page_content > #links { height: 24px; float: left; line-height: 22px; margin-right: 30px;}
        #page_content > #links > a { display: block; min-width: 10px; height: 22px; line-height: 22px; float: left; margin-left: 6px; padding: 0 6px; border: 1px solid rgb(236, 236, 236); text-align: center; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;color: #666;}
        #page_content > #links > a.this_page { background-color: #DB0B2D; border-color: #DB0B2D; color: #FFF; }
        #page_content > #links > a:hover { background-color: #DB0B2D; border-color: #DB0B2D; color: #FFF;}
        #page_content > #links > a:hover.no_page { background-color: #FFF; border-color: rgb(236, 236, 236); color: #666; cursor: default;}
        #page_content span{display:none}
        #page_content{font-size:10px;padding:10px 0}
    </style>
    <div id="page_content">
        <?php echo $this->getPageLink($page,$max,'',$limit);?>
    </div>

</div>
<?php include('side_slider.php');?>
</body>
</html>