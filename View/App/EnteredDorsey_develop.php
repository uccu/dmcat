<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>

<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('走进德汇');?></span>
        <form method="get" onsubmit="return false;">
            <!--input type="search" placeholder="<?php echo lang('输入关键字');?>"/-->
        </form>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('走进德汇');?> > <?php echo lang('发展历程');?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/EnteredDorsey_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang('发展历程');?></div>
            <div class="develop-nav">
                <a href="javascript:void(0);" class="develop-nav-left" onclick="navMove(1);"></a>
                <div class="develop-nav-box">
                    <div id="develop_box">

                        <?php 
                            
                            foreach($years as $i){
                                if($year == $i || (!$year && $i == $yearM))echo '<span class="checked">'.$i.'</span>';
                                else echo '<span>'.$i.'</span>';
                            }
                        ?>
                    </div>
                </div>
                <a href="javascript:void(0);" class="develop-nav-right" onclick="navMove(-1);"></a>
            </div>
            <div class="develop-content" style="min-height:500px">
                
                    <?php

                        foreach($list as $v){
                            echo '<div class="develop-one-month">
                            <span>'.$v->month.'月</span>
                            <div>
                                <h1>'.langV($v,'title').'</h1>
                                <h2>'.langV($v,'description').'</h2>
                            </div></div>';

                        }

                    ?>

                    
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    var obj_width = 84;
    var num = $("#develop_box > span").length;
    $("#develop_box").css("width", num * obj_width + "px");
    var no_move = true;

    $(document).ready(function () {
        $("#develop_box > span").click(function () {
            var obj = $(this);
            location = location.pathname+'?year='+obj.text();
        })
    });

    function navMove(type) {
        if (!no_move) {
            return false;
        }
        no_move = false;
        setTimeout(function () {
            no_move = true;
        }, 400);
        type = type == 1 ? 1 : -1;
        var obj = $("#develop_box");
        var left = parseInt(obj.css("margin-left"));
        if (type == -1 && left <= -(num - 10) * obj_width) {
            return false;
        }
        if (type == 1 && left >= 0) {
            return false;
        }
        obj.css("margin-left", left + (obj_width * type) + "px");
    }
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>