<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<style>
.video-box iframe{width:100%;height:100%}
</style>
<div class="video-box">
    <?php echo $info->video;?>
</div>
<div class="w-1200">
    <div class="video-info-title">
        <?php echo langV($info,'title');?> <em><?php echo lang('发布时间');?>：<?php echo date('Y-m-d',$info->create_time);?></em>
    </div>
    <div class="video-info-text">
        <?php echo langV($info,'content');?>
    </div>
    <div class="video-info-title">
        <?php echo lang('了解其它相关视频');?>
    </div>
    <div class="video-list info-other-list" style="width: 1200px;">
            <?php foreach($newsVideo as $k=>$v){?>
                <div class="video-list-one"<?php echo !$k%4?' style="margin-left: 0;"':'';?>>
                    <img src="/pic/<?php echo langV($v,'pic');?>">
                    <a href="/Home/News/videoPlay?id=<?php echo $v->id;?>" target="_blank"></a>
                    <h1><?php echo langV($v,'title',36);?></h1>
                </div>
                <?php }?>
        
        <div class="clear"></div>
    </div>
</div>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>