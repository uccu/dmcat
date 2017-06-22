<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/index_top.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<link rel="stylesheet" href="/Home/css/swiper.min.css">
<script type="text/javascript" src="/Home/js/swiper.jquery.min.js"></script>
<script type="text/javascript" src="/Home/js/yxMobileSlider.js"></script>
<div class="w-1200">
    <div class="slider">
        <ul>
            <?php
            foreach($banner as $v){
                if($v->href)echo '<li><a href="'.$v->href.'"><img src="/pic/'.$v->pic.'"></a></li>';
                else echo '<li><img src="/pic/'.$v->pic.'"></li>';
            }
            ?>
        </ul>
    </div>
</div>
<div class="w-1200">
    <div class="left-body">
        <div class="index-left">
            <div class="left-title"><?php echo lang('集团要闻');?> <a href="News/inNewsNews"><?php echo lang('查看更多');?>〉</a></div>
            <a href="News/newsInfo?id=<?php echo $newsGroup[0]->id;?>"><div class="left-content first">
                <img src="/pic/<?php echo $newsGroup[0]->pic;?>">
                <h1 style="    max-height: 48px;"><?php echo langV($newsGroup[0],'title',40);?></h1>
                <h2><?php echo langV($newsGroup[0],'description',70);?></h2>
            </div>
            </a><a href="News/newsInfo?id=<?php echo $newsGroup[1]->id;?>">
            <div class="left-content"><em>●</em><h3><?php echo langV($newsGroup[1],'title');?></h3><span><?php echo date('m-d',$newsGroup[1]->create_time);?></span></div>
            </a><a href="News/newsInfo?id=<?php echo $newsGroup[2]->id;?>">
            <div class="left-content"><em>●</em><h3><?php echo langV($newsGroup[2],'title');?></h3><span><?php echo date('m-d',$newsGroup[2]->create_time);?></span></div>
            </a><a href="News/newsInfo?id=<?php echo $newsGroup[3]->id;?>">
            <div class="left-content"><em>●</em><h3><?php echo langV($newsGroup[3],'title');?></h3><span><?php echo date('m-d',$newsGroup[3]->create_time);?></span></div>
            </a>
        </div>
        <div class="index-left">
            <div class="left-title"><?php echo lang('热点专题');?> <a href="News/special"><?php echo lang('查看更多');?>〉</a></div>
            <a href="News/specialInfo?id=<?php echo $newsHot[0]->id;?>"><div class="left-content"><em>●</em><h3><?php echo langV($newsHot[0],'title');?></h3><span><?php echo date('m-d',$newsHot[0]->create_time);?></span></div>
            </a><a href="News/specialInfo?id=<?php echo $newsHot[1]->id;?>"><div class="left-content"><em>●</em><h3><?php echo langV($newsHot[1],'title');?></h3><span><?php echo date('m-d',$newsHot[1]->create_time);?></span></div>
            </a><a href="News/specialInfo?id=<?php echo $newsHot[2]->id;?>"><div class="left-content"><em>●</em><h3><?php echo langV($newsHot[2],'title');?></h3><span><?php echo date('m-d',$newsHot[2]->create_time);?></span></div>
            </a><a href="News/specialInfo?id=<?php echo $newsHot[3]->id;?>"><div class="left-content"><em>●</em><h3><?php echo langV($newsHot[3],'title');?></h3><span><?php echo date('m-d',$newsHot[3]->create_time);?></span></div>
            </a>
        </div>
        <div class="index-left">
            <div class="left-title"><?php echo lang('媒体聚焦');?> <a href="News/media"><?php echo lang('查看更多');?>〉</a></div>
            <?php
                foreach($newsMedia as $v){
                    echo '<a href="News/mediaInfo?id='.$v->id.'"><div class="left-content"><em>●</em><h3>'.langV($v,'title').'</h3><span>'.date('m-d',$v->create_time).'</span></div></a>';
                }
            ?>
        </div>
        <div class="index-left">
            <div class="left-title"><?php echo lang('视频中心');?> <a href="News/video"><?php echo lang('查看更多');?>〉</a></div>
            <?php
                foreach($newsVideo as $v){
                    echo 
                        '<a href="News/videoPlay?id='.$v->id.'"><div class="one-video">
                            <img src="/pic/'.$v->pic.'">
                            <a href="javascript:void(0);" class="play_btn"></a>
                            <h4>'.langV($v,'title').'</h4>
                        </div></a>';
                }
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="right-body">
        <div class="right-banner">
            <div class="banner-title">
                
                <style>
                    .banner-title a.checked:after{left: 47%;}
                    .banner-title a{width:auto;margin-left: 59px;}
                    .banner-title a[data-num="1"]{margin-left: 0px;}
                </style>
                <?php if($this->L->lang == 'en'){?>
                <style>
                    .banner-title a{width:auto;margin-left: 31px;}
                </style>
                <?php }?>
                
                
                <a href="javascript:void(0);" class="checked" data-num="1"><?php echo lang('德汇宝贝广场');?></a>
                <a href="javascript:void(0);" data-num="2"><?php echo lang('德汇万达广场');?></a>
                <a href="javascript:void(0);" data-num="3"><?php echo lang('德汇物流');?></a>
                <a href="javascript:void(0);" data-num="4"><?php echo lang('德汇特色小镇');?></a>
                <a href="javascript:void(0);" data-num="5"><?php echo lang('德汇金融');?></a>
                

            </div>
            <div class="banner-box">
                <!-- 德汇新天地 开始 -->
                <div class="one-banner">
                    <div class="slider_1" id="slider_1">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                        </div>
                        <!-- 如果需要分页器 -->
                        <div class="swiper-pagination"></div>
                        <div class="this-title"><?php echo lang('德汇宝贝广场');?></div>
                    </div>
                    <h1>2015年12月，德汇集团与国际商业巨头万达集团达成战略合作协议，联手在全国建设十座“德汇万达广场”。目前，14万方的德汇万达广场将于2017年下半年开业，该项目是德汇新天地的超级单体主力店。<a href="Domain/newWorld">【了解更多】</a></h1>
                </div>
                <!-- 德汇新天地 结束 -->

                <!-- 万达广场 开始 -->
                <div class="one-banner">
                    <div class="slider_1" id="slider_2">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                        </div>
                        <!-- 如果需要分页器 -->
                        <div class="swiper-pagination"></div>
                        <div class="this-title"><?php echo lang('德汇万达广场');?></div>
                    </div>
                    <h1>2015年12月，德汇集团与国际商业巨头万达集团达成战略合作协议，联手在全国建设十座“德汇万达广场”。目前，14万方的德汇万达广场将于2017年下半年开业，该项目是德汇新天地的超级单体主力店。<a href="Domain/wandaSquare">【了解更多】</a></h1>
                </div>
                <!-- 万达广场 结束 -->
                <!-- 亚欧新城 开始 -->
                <div class="one-banner">
                    <div class="slider_1" id="slider_3">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                        </div>
                        <!-- 如果需要分页器 -->
                        <div class="swiper-pagination"></div>
                        <div class="this-title"><?php echo lang('德汇特色小镇');?></div>
                    </div>
                    <h1>2015年12月，德汇集团与国际商业巨头万达集团达成战略合作协议，联手在全国建设十座“德汇万达广场”。目前，14万方的德汇万达广场将于2017年下半年开业，该项目是德汇新天地的超级单体主力店。<a href="Domain/logistics">【了解更多】</a></h1>
                </div>
                <!-- 亚欧新城 结束 -->
                <!-- 德汇金融 开始 -->
                <div class="one-banner">
                    <div class="slider_1" id="slider_4">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                        </div>
                        <!-- 如果需要分页器 -->
                        <div class="swiper-pagination"></div>
                        <div class="this-title"><?php echo lang('德汇物流');?></div>
                    </div>
                    <h1>2015年12月，德汇集团与国际商业巨头万达集团达成战略合作协议，联手在全国建设十座“德汇万达广场”。目前，14万方的德汇万达广场将于2017年下半年开业，该项目是德汇新天地的超级单体主力店。<a href="Domain/newCity">【了解更多】</a></h1>
                </div>
                <!-- 德汇金融 结束 -->
                <!-- 德汇金融 开始 -->
                <div class="one-banner">
                    <div class="slider_1" id="slider_5">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                            <div class="swiper-slide top-banner">
                                <img src="/Home/images/index/dhxtd.png" alt="">
                            </div>
                        </div>
                        <!-- 如果需要分页器 -->
                        <div class="swiper-pagination"></div>
                        <div class="this-title"><?php echo lang('德汇物流');?></div>
                    </div>
                    <h1>2015年12月，德汇集团与国际商业巨头万达集团达成战略合作协议，联手在全国建设十座“德汇万达广场”。目前，14万方的德汇万达广场将于2017年下半年开业，该项目是德汇新天地的超级单体主力店。<a href="Domain/finance">【了解更多】</a></h1>
                </div>
                <!-- 德汇金融 结束 -->
                
            </div>
            <div class="right-content">
                <div class="right-title"><?php echo lang('发展历程');?></div>
                <a href="EnteredDorsey/develop"><img src="/pic/<?php echo $this->L->config->home_development_history;?>" style="height: 130px;"></a>
            </div>
            <div class="right-content" style="margin-top: 36px;">
                <div class="right-title"><?php echo lang('社会责任');?></div>
                <a href="EnteredDorsey/blame"><img src="/pic/<?php echo $this->L->config->home_social_responsibility;?>" style="height:203px;"></a>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    $(".slider").yxMobileSlider({width: 1200, height: 400, during:6000});
    for (var i = 1; i <= 5; i++) {
        var swiper = new Swiper('#slider_' + i, {
            direction: 'horizontal',
            loop: true,
            pagination: '.swiper-pagination',//分页显示设置
            autoplay: 6000,
            width: 670,
            paginationClickable: true,
            nested:true,
            paginationBulletRender: function (swiper, index, className) {
                return '<span class="' + className + '">' + (index + 1) + '</span>';
            }
        });
    }
    $(document).ready(function () {
        $(".banner-title a").click(function () {
            var obj = $(this);
            if (obj.hasClass("checked")) {
                return false;
            }
            obj.addClass("checked").siblings(".checked").removeClass("checked");
            var num = parseInt(obj.attr("data-num")) - 1;
            $(".banner-box").css("margin-left", - 670 * num + "px");
        });
    });
</script>
<?php include(VIEW_ROOT.'App/Common/index_bottom.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>