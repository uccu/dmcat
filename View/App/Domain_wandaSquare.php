<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<style type="text/css">
    .develop-one-month div h1{font-size: 14px; color: #676767;}
    .profile_info{font-size: 13px;}
    .profile_info > p{padding-top: 0;}
    .profile_info img{margin: 10px 0;}
</style>
<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('走进德汇');?></span>
        <form method="get" onsubmit="return false;">
            <!--input type="search" placeholder="<?php echo lang('输入关键字');?>"/-->
        </form>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('德汇产业');?> > <?php echo lang($name);?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/Domain_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang($name);?></div>
            <div class="culture_content">
                <?php echo langV($page,'content');?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>