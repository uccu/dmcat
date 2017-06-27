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
<div class="video-nav" style="overflow-x: scroll; overflow-y: hidden;">
    <div class="video-nav-top">
        <a href="javascript:void(0);" data-id="0"<?php echo $video_type==0?' class="active"':'';?>>全部视频</a>
        <?php foreach($newsVideoType as $v){?>
        <a href="javascript:void(0);" data-id="<?php echo $v->id;?>"<?php echo $video_type==$v->id?' class="active"':'';?>><?php echo langV($v,'name');?></a>
        <?php }?>
        <script>
        $('.video-nav-top a').click(function(){$.post('/user/video_type',{l:$(this).attr('data-id')},function(){location.reload()})})
        </script>
        
    </div>
</div>


<?php if($page == 1 && $video_type==0 && $banner){?>
<div class="video-banner swiper-container">
    <div class="swiper-wrapper">
        <?php foreach($banner as $v){?>
        <div class="video-one-banner swiper-slide">
            <img src="/pic/<?php echo langV($v,'pic');?>">
            <a href="videoPlay?id=<?php echo $v->id;?>"></a>
            <h1><?php echo langV($v,'title',60);?></h1>
            <span>
                <?php echo langV($v,'description',300);?>
            </span>
        </div>
        <?php }?>
    </div>
    <div class="swiper-pagination"></div>
</div>
<?php }?>

<div class="video-list row">
    <?php foreach($newsVideo as $k=>$v){?>

    <div class="video-list-one col-md-4 col-xs-4 column">
        <img src="/pic/<?php echo langV($v,'pic');?>">
        <a href="videoPlay?id=<?php echo $v->id;?>" ></a>
        <h1><?php echo langV($v,'title',18);?></h1>
    </div>
    <?php }?>
    
</div>

<?php include('side_slider.php');?>
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