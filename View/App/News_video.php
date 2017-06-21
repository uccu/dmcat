<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<link rel="stylesheet" href="/Home/css/swiper.min.css">
<script type="text/javascript" src="/Home/js/swiper.jquery.min.js"></script>
<div class="w-1200">
    <div class="content-top">
        <span>新闻中心</span>
        <form method="get" onsubmit="return false;">
            <select>
                <option value="">选择年份</option>
                <option value="2017">2017</option>
                <option value="2016">2016</option>
                <option value="2015">2015</option>
                <option value="2014">2014</option>
                <option value="2013">2013</option>
                <option value="2012">2012</option>
                <option value="2011">2011</option>
            </select>
            <input type="search" placeholder="输入关键字"/>
        </form>
    </div>
    <div class="this-address">首页 > 新闻中心 > 视频中心</div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/News_left.php');?>
        <div class="content-right">
            <div class="content-title">视频中心
                <div class="video-nav">
                    <a href="javascript:void(0);" data-id="0"<?php echo $video_type==0?' class="checked"':'';?>>全部视频</a>
                    <?php foreach($newsVideoType as $v){?>

                    <a href="javascript:void(0);" data-id="<?php echo $v->id;?>"<?php echo $video_type==$v->id?' class="checked"':'';?>><?php echo langV($v,'name');?></a>
                    <?php }?>
                    <script>
                        $('.video-nav a').click(function(){$.post('/user/video_type',{l:$(this).attr('data-id')},function(){location.reload()})})
                    </script>
                </div>
            </div>
            <?php if($page == 1 && $video_type==0 && $banner){?>
            <div class="video-banner">
                <div class="swiper-wrapper">
                <?php foreach($banner as $v){?>
                    <div class="video-one-banner swiper-slide">
                        <img src="/pic/<?php echo langV($v,'pic');?>">
                        <a href="/Home/News/videoPlay?id=<?php echo $v->id;?>" target="_blank"></a>
                        <h1><?php echo langV($v,'title',60);?></h1>
                        <span><?php echo langV($v,'description',300);?></span>
                    </div>
                <?php }?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <?php }?>
            <div class="video-list">
                <?php foreach($newsVideo as $k=>$v){?>
                <div class="video-list-one"<?php echo !$k%4?' style="margin-left: 0;"':'';?>>
                    <img src="/pic/<?php echo langV($v,'pic');?>">
                    <a href="/Home/News/videoPlay?id=<?php echo $v->id;?>" target="_blank"></a>
                    <h1><?php echo langV($v,'title');?></h1>
                </div>
                <?php }?>
                
                <div class="clear"></div>
            </div>
            <div id="page_content">
                <?php echo $this->getPageLink($page,$max,'',$limit);?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    var swiper = new Swiper('.video-banner', {
        direction: 'horizontal',
        loop: true,
        pagination: '.swiper-pagination',//分页显示设置
        autoplay: 6000,
        width: 970,
        paginationClickable: true,
        nested:true,
    });
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>