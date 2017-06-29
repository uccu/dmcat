<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('新闻中心');?></span>
        <form id="search" method="post" onsubmit="return false;">
            <select name="type">
                <option value="1"><?php echo lang('集团要闻');?></option>
                <option value="2"><?php echo lang('热点专题');?></option>
                <option value="3"><?php echo lang('媒体聚焦');?></option>
                <option value="4"><?php echo lang('视频中心');?></option>
            </select>
            <input type="search" name="search" value="" placeholder="<?php echo lang('输入关键字');?>"/>
        </form>
        <script>
            $('form#search').submit(function(){
                $.post('/news/search',$(this).serialize(),function(d){
                    if(d.url)location = d.url
                },'json')
                return false
            })
        </script>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('新闻中心');?> > <?php echo lang($name);?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/News_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang($name);?></div>
            <?php foreach($list as $info){?>
                <div class="news-special-content">
                    <div class="special-left"><?php echo date('m-d',$info->create_time);?><em><?php echo date('Y',$info->create_time);?></em></div>
                    <img src="/pic/<?php echo $info->pic;?>">
                    <div class="special-right">
                        <a href="/Home/News/mediaInfo?id=<?php echo $info->id;?>"><h1><?php echo langV($info,'title');?></h1></a>
                        <h2><?php echo langV($info,'description',150);?></h2>
                    </div>
                </div>
            <?php }?>
            <div id="page_content">
                <?php echo $this->getPageLink($page,$max,'',$limit);?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>