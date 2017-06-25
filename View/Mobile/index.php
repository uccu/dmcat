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
    <title>德汇集团</title>
    <link rel="stylesheet" href="css/swiper.min.css">
    <link href="css/reset.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script src="js/swiper.jquery.min.js"></script>
</head>
<body>
    <header>
        <div class="header-left index-header-left" onclick="redirect('seacher.html')"></div>
        <div class="header-center"></div>
        <div class="header-right"></div>
    </header>
    <p class="fenge"></p>
    <div class="swiper-container index-banner">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="img/1.png" alt="">
            </div>
            <div class="swiper-slide">
                <img src="img/2.png" alt="">
            </div>
            <div class="swiper-slide">
                <img src="img/3.png" alt="">
            </div>
        </div>
        <!-- 如果需要分页器 -->
        <div class="swiper-pagination"></div>
    </div>

    <div class="main-box">
        <h1>社会责任</h1>
        <h3>SOCIAL RESPONSIBILITY</h3>
        <img src="/pic/<?php echo $this->L->config->home_social_responsibility;?>" onclick="redirect('responsibility.html')">
    </div>
    <div class="main-box">
        <h1>发展历程</h1>
        <h3>DEVELOPMENT HISTORY</h3>
        <img src="<?php echo $this->L->config->home_development_history;?>" onclick="redirect('development.html')">
        <!--P>一部商业文明的现代经注始于1985年，从中国东部 出发的年轻人，奔赴西边边陲，开始创业之路。</P-->
        <div class="more-button">
            <a href="development.html">查看更多</a>
        </div>
    </div>
    <div class="main-box">
        <div class="tabchange-nav video-nav" style="overflow-x: scroll; overflow-y: hidden;">
            <ul class="tabchange" >
                <li class="tabchange-active">德汇宝贝广场</li>
                <li>德汇万达广场</li>
                <li>德汇特色小镇</li>
                <li>德汇物流</li>
                <li>德汇金融</li>
            </ul>
        </div>
        <div class="tabcontain" style="display: block;">
                <div class="swiper-container tabslider" id="slider_1">
                    <div class="swiper-wrapper">
                        <?php if($homeM[0]->pic)foreach(explode(';',$homeM[0]->pic) as $pic){echo '<div class="swiper-slide top-banner"><img src="/pic/'.$pic.'" alt=""></div>';}?>
                        
                    </div>
                    <!-- 如果需要分页器 -->
                    <div class="swiper-pagination tab-pagination"></div>
                    <div class="this-title">
                        <?php echo lang('德汇宝贝广场');?>
                    </div>
                </div>
                <p><?php echo langV($homeM[0],'description');?></p>
                <div class="more-button">
                    <a href="newWorld">了解更多</a>
                </div>
        </div>
        <div class="tabcontain">
                <div class="swiper-container tabslider" id="slider_2">
                    <div class="swiper-wrapper">
                        <?php if($homeM[1]->pic)foreach(explode(';',$homeM[1]->pic) as $pic){echo '<div class="swiper-slide top-banner"><img src="/pic/'.$pic.'" alt=""></div>';}?>
                        
                    </div>
                    <!-- 如果需要分页器 -->
                    <div class="swiper-pagination tab-pagination"></div>
                    <div class="this-title">
                        <?php echo lang('德汇万达广场');?>
                    </div>
                </div>
                <p><?php echo langV($homeM[1],'description');?></p>
                <div class="more-button">
                    <a href="wandaSquare">了解更多</a>
                </div>
        </div>
        <div class="tabcontain">
                <div class="swiper-container tabslider" id="slider_3">
                    <div class="swiper-wrapper">
                        <?php if($homeM[2]->pic)foreach(explode(';',$homeM[2]->pic) as $pic){echo '<div class="swiper-slide top-banner"><img src="/pic/'.$pic.'" alt=""></div>';}?>
                        
                    </div>
                    <!-- 如果需要分页器 -->
                    <div class="swiper-pagination tab-pagination"></div>
                    <div class="this-title">
                        <?php echo lang('德汇特色小镇');?>
                    </div>
                </div>
                <p><?php echo langV($homeM[2],'description');?></p>
                <div class="more-button">
                    <a href="newCity">了解更多</a>
                </div>
        </div>

        <div class="tabcontain">
                <div class="swiper-container tabslider" id="slider_4">
                    <div class="swiper-wrapper">
                        <?php if($homeM[3]->pic)foreach(explode(';',$homeM[3]->pic) as $pic){echo '<div class="swiper-slide top-banner"><img src="/pic/'.$pic.'" alt=""></div>';}?>
                        
                    </div>
                    <!-- 如果需要分页器 -->
                    <div class="swiper-pagination tab-pagination"></div>
                    <div class="this-title">
                        <?php echo lang('德汇物流');?>
                    </div>
                </div>
                <p><?php echo langV($homeM[3],'description');?></p>
                <div class="more-button">
                    <a href="logistics">了解更多</a>
                </div>
        </div>

        <div class="tabcontain">
                <div class="swiper-container tabslider" id="slider_5">
                    <div class="swiper-wrapper">
                        <?php if($homeM[4]->pic)foreach(explode(';',$homeM[4]->pic) as $pic){echo '<div class="swiper-slide top-banner"><img src="/pic/'.$pic.'" alt=""></div>';}?>
                        
                    </div>
                    <!-- 如果需要分页器 -->
                    <div class="swiper-pagination tab-pagination"></div>
                    <div class="this-title">
                        <?php echo lang('德汇金融');?>
                    </div>
                </div>
                <p><?php echo langV($homeM[4],'description');?></p>
                <div class="more-button">
                    <a href="finance">了解更多</a>
                </div>
        </div>

        
    </div>
    <div class="news-box">
        <ul class="news-box-title">
            <li>
                <h1>热点专题</h1>
                <h3>HOT TOPIC</h3>
            </li>
            <li>
                <h1 class="news-active">集团要闻</h1>
                <h3>CROUP NEWS</h3>
            </li>
            <li>
                <h1>媒体聚焦</h1>
                <h3>MEDIA FOCUS</h3>
            </li>
        </ul>
        <div class="news-tabcontain">
            <ul>
                <?php foreach($newsHot as $v){?>
                <li>
                    <em>.</em>
                    <a href="specialInfo?id=<?php echo $v->id;?>">
                        <?php echo langV($v,'title',36);?>
                    </a>
                    <span>
                        <?php echo date('m-d',$v->create_time);?>
                    </span>
                </li>
                <?php }?>
            </ul>
            <div class="more-button">
                <a href="special">查看更多</a>
            </div>
        </div>
        <div class="news-tabcontain" style="display: block">
            <div class="swiper-container swiper-container-horizontal new-flashSalebanner">
                <div class="swiper-wrapper">
                    <?php foreach($newsGroup as $v){?>
                    <div class="swiper-slide">
                        <img src="/pic/<?php echo $v->pic;?>"  class="main-img">
                    </div>
                    <?php }?>
                </div>
                <style>a{color:inherit}</style>
                <?php foreach($newsGroup as $v){?>
                    <div class="slider-text">
                    <h1><a href="newsInfo?id=<?php echo $v->id;?>"><?php echo langV($v,'title');?></a></h1>
                    <span></span>
                    
                </div>
                <?php }?>
                
                <div class="more-button">
                        <a href="inNews">查看更多</a>
                    </div>
            </div>


        </div>
        <div class="news-tabcontain">
            <ul>
                <?php foreach($newsMedia as $v){?>
                <li>
                    <em>.</em>
                    <a href="specialInfo?id=<?php echo $v->id;?>">
                        <?php echo langV($v,'title',36);?>
                    </a>
                    <span>
                        <?php echo date('m-d',$v->create_time);?>
                    </span>
                </li>
                <?php }?>
            </ul>
            <div class="more-button">
                <a href="media">查看更多</a>
            </div>
        </div>

    </div>
    <div class="main-box">
        <h1>视频</h1>
        <h3>VIDEO</h3>
        <div class="index-video">
            <a href="videoPlay?id=<?php echo $newsVideo[0]->id;?>" ></a>
            <img src="/pic/<?php echo $newsVideo[0]->pic;?>">
        </div>
        <div class="more-button">
            <a href="video">查看更多</a>
        </div>
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
            <img src="img/celogo.png" alt="">
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
    <script>

        window.onload = function(){
            var mySwiper = new Swiper ('.index-banner', {
                direction: 'horizontal',
                loop: true,
                autoplay: 3000,
                pagination: '.swiper-pagination',
            })
            for (var i = 1; i <= 3; i++) {
                var swiper = new Swiper('#slider_' + i, {
                    direction: 'horizontal',
                    loop: true,
                    autoplay:1500,
                    pagination: '.tab-pagination',//分页显示设置
                    paginationBulletRender: function (swiper, index, className) {
                        return '<span class="' + className + '">' + (index + 1) + '</span>';
                    }
                });
            }
        }
        $(".tabcontain").removeClass('hid').addClass('hid')
        $(".tabchange").find('li').click(function(){
            $(".tabcontain").css('display','none');
            $(this).addClass('tabchange-active');
            $(this).siblings().removeClass('tabchange-active')
            $('.tabcontain').eq($(this).index()).css('display','block')
        })

        $(".news-box-title").find('li').click(function(){
            $(this).find("h1").addClass("news-active");
            $(this).siblings().find("h1").removeClass("news-active");
            $(".news-tabcontain").css("display","none");
            $(".news-tabcontain").eq($(this).index()).css("display","block")
        })

        //限时抢购轮播图
        var flashSwiper = new Swiper(".new-flashSalebanner", {
            slidesPerView: "auto",
            loop : true,
            centeredSlides : true,
            watchSlidesProgress: !0,
            onSlideChangeStart: function(swiper){
//                console.info(swiper.activeIndex)
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex+1).addClass('img').removeClass('imgs')
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex-1).addClass('img').removeClass('imgs')
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex).removeClass('img').addClass('imgs')
                $('.slider-text').eq(swiper.activeIndex).css('display','block');
                console.info($('.slider-text').index())
            },
            onProgress: function(swiper){
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex+1).addClass('img').removeClass('imgs')
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex-1).addClass('img').removeClass('imgs')
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex).removeClass('img').addClass('imgs')
            },
        });

        var htmlSize = parseFloat($('html').css("font-size"));
        var obj = $(".tabchange");
        var width = 0;
        $(".tabchange li").each(function(){
            width += parseFloat($(this).css("width")) + htmlSize * 0.6;
        });
        obj.css("width", width + "px");

    </script>
</body>
</html>