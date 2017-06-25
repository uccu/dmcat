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
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/swiper.min.css">
    <link href="css/reset.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/jquery-1.12.3.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script src="js/swiper.jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
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
<!--div class="video-nav" style="overflow-x: scroll; overflow-y: hidden;">
    <div class="video-nav-top">
        <a href="javascript:void(0);" class="active">全部视频</a>
        <a href="javascript:void(0);">董事长演讲</a>
        <a href="javascript:void(0);">德汇宣传片</a>
        <a href="javascript:void(0);">企业活动</a>
        <a href="javascript:void(0);">媒体报道</a>
        <a href="javascript:void(0);">战略合作</a>
    </div>
</div-->
<div class="video-banner">
    <div class="video-one-banner">
        
        <?php echo $info->video;?>
        <!--<a href="#" ></a>-->
        <h1><?php echo langV($info,'title');?></h1>
        <style>
            .content p{color:#fff !important}
        </style>
        <div class="content" style>
            <?php echo langV($info,'content');?>
        </div>
    </div>
</div>

<div class="video-list row">
     <?php foreach($newsVideo as $k=>$v){?>

    <div class="video-list-one col-md-4 col-xs-4 column">
        <img src="/pic/<?php echo langV($v,'pic');?>">
        <a href="videoPlay?id=<?php echo $v->id;?>" ></a>
        <h1><?php echo langV($v,'title',18);?></h1>
    </div>
    <?php }?>
  
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
            <li><a href="inNews">集团要闻</a></li>
            <li><a href="special">热点专题</a></li>
            <li><a href="media">媒体聚焦</a></li>
            <li><a href="video">视频中心</a></li>
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
<script type="text/javascript">
    var myswiper = new Swiper('.video-banner', {
        direction: 'horizontal',
        loop: true,
        pagination: '.swiper-pagination',//分页显示设置
        autoplay: 6000,
        paginationClickable: true,
        nested:true,
    });
    setBannerWidth();
    $(".video-nav").find("a").click(function(){
        $(this).addClass('active')
        $(this).siblings().removeClass("active");
//        $(".video-nav").scrollLeft($(".video-nav li").eq($(this).index()).offset().left-$('.video-nav-top').offset().left-$(window).width()/2);
    })
</script>


</body>
</html>