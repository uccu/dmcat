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
<footer>
    <p>
        <span>
            <img src="img/footerlogo.png" alt="">
            ©2013-2018
        </span>
        <span>德汇集团版权所有</span>
    </p>
    <p>
        <span>电话： 0991-7788916</span>
        <span>传真： 0991-5811770</span>
        <span>地址： 中国乌鲁木齐市奇台路658号</span>
    </p>
</footer>

<!--侧边栏-->
<div class="sidebar-box">
    <div class="sidebar-header">
        <img src="img/celogo.png" onclick="redirect('index.html')">
    </div>
    <div class="sidebar-main">
        <h1>
            <span>+</span>
            走进德汇
        </h1>
        <ul>
            <li><a href="groupProfile.html">集团简介</a></li>
            <li><a href="chairman.html">董事长专区</a></li>
            <li><a href="development.html">发展历程</a></li>
            <li><a href="cultural.html">企业文化</a></li>
            <li><a href="honor.html">企业荣誉</a></li>
            <li><a href="responsibility.html">社会责任</a></li>
        </ul>
    </div>
    <div class="sidebar-main">
        <h1>
            <span>+</span>
            德汇产业
        </h1>
        <ul>
            <li><a href="#">德汇宝贝广场</a></li>
            <li><a href="#">德汇万达广场</a></li>
            <li><a href="#">德汇特色小镇</a></li>
            <li><a href="#">德汇物流</a></li>
            <li><a href="#">德汇金融</a></li>
            <li><a href="#">德汇教育</a></li>
        </ul>
    </div>
    <div class="sidebar-main">
        <h1>
            <span>+</span>
            新闻中心
        </h1>
        <ul>
            <li><a href="groupnews.html">集团要闻</a></li>
            <li><a href="hottopic.html">热点专题</a></li>
            <li><a href="#">媒体聚焦</a></li>
            <li><a href="videos.html">视频中心</a></li>
        </ul>
    </div>
    <div class="sidebar-main">
        <h1>
            <span>+</span>
            联系我们
        </h1>
        <ul>
            <li><a href="contentus.html">德汇招聘</a></li>
            <li><a href="tenders.html">招标公告</a></li>
            <li><a href="advice.html">投诉与建议</a></li>
            <li><a href="eval.html">法律声明</a></li>
        </ul>
    </div>
</div>
<script src="js/sidebar.js"></script>
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