<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('新闻中心');?></span>
        <form method="get" onsubmit="return false;">
            <select>
                <option value=""><?php echo lang('选择年份');?></option>
                <?php 
                    $yearM = date('Y');
                    for($i = $yearM;$i>2000;$i--){
                        echo '<option value="'.$i.'">'.$i.'</option>';
                    }
                ?>
            </select>
            <input type="search" placeholder="<?php echo lang('输入关键字');?>"/>
        </form>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('新闻中心');?> > <?php echo lang($name);?> > <?php echo langV($info,'title',50);?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/News_left.php');?>
        <div class="content-right">
            <div class="newsInfoContent" style="min-height:700px">
                <div class="news-title"><?php echo langV($info,'title');?></div>
                <div class="news-time">发布时间：<?php echo date('Y-m-d',$info->create_time);?><span></span>浏览次数：<?php echo $info->browse;?></div>
                <?php echo langV($info,'content');?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>