<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<style type="text/css">
    .profile_info p{padding-top: 0;}
    .profile_info{line-height: 30px;}
</style>
<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('走进德汇');?></span>
        <form method="get" onsubmit="return false;">
            <!--input type="search" placeholder="<?php echo lang('输入关键字');?>"/-->
        </form>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('走进德汇');?> > <?php echo lang('社会责任');?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/EnteredDorsey_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang('社会责任');?></div>
            <div class="culture_content">
                <?php echo langV($page,'content');?>
                <div class="profile_info" style="margin-top: 20px;">
                    <h6><?php echo lang('德汇慈善事业');?></h6>
                    <div class="beneficence-box">

                    <?php
                        foreach($charitable as $v){

                            echo '<div class="beneficence-content">
                            <img src="/pic/'.$v->pic.'">
                            <h1>'.$v->year.'年'.$v->month.'月</h1>
                            <h2>'.langV($v,'description').'</h2>
                        </div>';
                        }
                    
                    ?>
                        
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>