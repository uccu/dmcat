<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>

<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('走进德汇');?></span>
        <form method="get" onsubmit="return false;">
            <input type="search" placeholder="<?php echo lang('输入关键字');?>"/>
        </form>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('走进德汇');?> > <?php echo lang('集团简介');?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/EnteredDorsey_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang('集团简介');?></div>
            <?php echo langV($page,'content');?>

            <?php
                foreach($introductionProduct as $v){

                    echo '<div class="product_title">'.langV($v,'title').'</div>
                    <div class="product_content">
                        <img src="/pic/'.$v->pic.'">
                        <h1>'.langV($v,'title').'</h1>';
                    echo langV($v,'content');
                    echo '</div>';
                }
            
            ?>

        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".product_title").click(function () {
            var obj = $(this);
            if (obj.hasClass("show")) {
                obj.removeClass("show").next().css("display", "none");
            } else {
                obj.addClass("show").next().css("display", "block");
            }
        });
    });
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>