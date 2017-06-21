<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<style type="text/css">
    .culture_content p{color: #787878; font-size: 12px; line-height: 20px; padding: 10px 0;}
</style>
<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('联系我们');?></span>
        
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('联系我们');?> > <?php echo lang($name);?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/CountUs_left.php');?>
        <div class="content-right">
            <?php echo langV($page,'content');?>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>