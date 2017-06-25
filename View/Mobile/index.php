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
            <?php
            foreach($banner as $v){
                if($v->href)echo '<div class="swiper-slide"><a href="'.$v->href.'"><img src="/pic/'.$v->pic.'"></a></div>';
                else echo '<div class="swiper-slide"><img src="/pic/'.$v->pic.'"></div>';
            }
            ?>
            
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
        <ul class="tabchange">
            <li class="tabchange-active">德汇宝贝广场</li>
            <li>德汇万达广场</li>
            <li>德汇特色小镇</li>
            <li>德汇物流</li>
        </ul>
        <div class="tabcontain" style="display: block;">
                <div class="swiper-container tabslider" id="slider_1">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                        <div class="swiper-slide top-banner">
                            <img src="img/tabbanner.png">
                        </div>
                        <div class="swiper-slide top-banner">
                            <img src="img/tabbanner.png">
                        </div>
                    </div>
                    <!-- 如果需要分页器 -->
                    <div class="swiper-pagination tab-pagination"></div>
                    <div class="this-title">德汇宝贝广场</div>
                </div>
                <p>2015年12月，德汇集团与国际商业巨头万达集团达成战略合作协议，联手在全国建设十座“德汇万达广场”。目前，14万方的德汇万达广场将于2017年下半年开业，该项目是德汇新天地的超级单体主力店。</p>
                <div class="more-button">
                    <a href="javascript:void(0)">了解更多</a>
                </div>
        </div>
        <div class="tabcontain">
            <div class="swiper-container tabslider" id="slider_2">
                <div class="swiper-wrapper">
                    <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                    <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                    <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                </div>
                <!-- 如果需要分页器 -->
                <div class="swiper-pagination tab-pagination"></div>
                <div class="this-title">德汇万达广场</div>
            </div>
            <p>2015年12月，德汇集团与国际商业巨头万达集团达成战略合作协议，联手在全国建设十座“德汇万达广场”。目前，14万方的德汇万达广场将于2017年下半年开业，该项目是德汇新天地的超级单体主力店。</p>
            <div class="more-button">
                <a href="javascript:void(0)">了解更多</a>
            </div>
        </div>
        <div class="tabcontain">
            <div class="swiper-container tabslider" id="slider_3">
                <div class="swiper-wrapper">
                    <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                    <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                    <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                    <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                    <div class="swiper-slide top-banner">
                        <img src="img/tabbanner.png">
                    </div>
                </div>
                <!-- 如果需要分页器 -->
                <div class="swiper-pagination tab-pagination"></div>
                <div class="this-title">德汇特色小镇</div>
            </div>
            <p>2015年12月，德汇集团与国际商业巨头万达集团达成战略合作协议，联手在全国建设十座“德汇万达广场”。目前，14万方的德汇万达广场将于2017年下半年开业，该项目是德汇新天地的超级单体主力店。</p>
            <div class="more-button">
                <a href="javascript:void(0)">了解更多</a>
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
                <li>
                    <em>.</em>
                    <a href="javascript:void(0)">
                        新德汇 新商业 新起点-——德汇四大产品线四大项目全新亮相
                    </a>
                    <span>06-12</span>

                </li>
                <li>
                    <em>.</em>
                    <a href="javascript:void(0)">
                        新德汇 新商业 新起点-——德汇四大产品线四大项目全新亮相
                    </a>
                    <span>06-12</span>

                </li>
                <li>
                    <em>.</em>
                    <a href="javascript:void(0)">
                        新德汇 新商业 新起点-——德汇四大产品线四大项目全新亮相
                    </a>
                    <span>06-12</span>
                </li>
            </ul>
            <div class="more-button">
                <a href="hottopic.html">查看更多</a>
            </div>
        </div>
        <div class="news-tabcontain" style="display: block">
            <div class="swiper-container swiper-container-horizontal new-flashSalebanner">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="img/bannerchild1.png"  class="main-img">
                    </div>
                    <div class="swiper-slide">
                        <img src="img/bannerchild1.png"  class="main-img">
                    </div>
                    <div class="swiper-slide">
                        <img src="img/banner.png"  class="main-img">
                    </div>
                </div>
            </div>
            <h1>德汇——中心项目助力产业升级 沙区政府领导莅临参观指导</h1>
            <span>5月2日下午7点，沙依巴克区人民政府经发委、国税局、地税局、统计局、财务局等重点委办局一行领导莅临德汇集团，并召开座谈会。</span>
            <div class="more-button">
                <a href="groupnews.html">查看更多</a>
            </div>
        </div>
        <div class="news-tabcontain">
            <ul>
                <li>
                    <em>.</em>
                    <a href="javascript:void(0)">
                        学习万达 强化管理————对违规违纪人员的通报
                    </a>
                    <span>06-12</span>

                </li>
                <li>
                    <em>.</em>
                    <a href="javascript:void(0)">
                        【趋势】德汇儿童体验式产业商业模式生命力旺盛地产巨头万达进军儿童产业
                    </a>
                    <span>06-12</span>

                </li>
                <li>
                    <em>.</em>
                    <a href="javascript:void(0)">
                        专题报道：55万方城市生活中心+市中心最大城市公园=生态型商业产业群
                    </a>
                    <span>06-12</span>
                </li>
            </ul>
            <div class="more-button">
                <a href="javascript:void(0)">查看更多</a>
            </div>
        </div>

    </div>
    <div class="main-box">
        <h1>视频</h1>
        <h3>VIDEO</h3>
        <div class="index-video">
            <a href="#" target="_blank"></a>
            <img src="img/video1.png">
        </div>
        <div class="more-button">
            <a href="videos.html">查看更多</a>
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
//            spaceBetween:30,
            loop : true,
            centeredSlides : true,
            watchSlidesProgress: !0,
            onSlideChangeStart: function(swiper){
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex+1).addClass('img').removeClass('imgs')
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex-1).addClass('img').removeClass('imgs')
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex).removeClass('img').addClass('imgs')
            },
            onProgress: function(swiper){
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex+1).addClass('img').removeClass('imgs')
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex-1).addClass('img').removeClass('imgs')
                $(".new-flashSalebanner .swiper-slide").eq(swiper.activeIndex).removeClass('img').addClass('imgs')
            },
        });

    </script>
</body>
</html>