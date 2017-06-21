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
            <input type="search" placeholder="<?php echo lang('输入关键字');?>"/>
        </form>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('走进德汇');?> > <?php echo lang('企业文化');?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/EnteredDorsey_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang('企业文化');?>
                <div class="video-nav">
                    <a href="javascript:void(0);" class="checked" onclick="changeType(this, 1);"><?php echo lang('文化体系');?></a>
                    <a href="javascript:void(0);" onclick="changeType(this, 2);"><?php echo lang('企业内刊');?></a>
                </div>
            </div>
            <div class="culture_content">
                <?php echo langV($page,'content');?>
            </div>
            <div class="enterprise_info" style="display: none;">
                <div class="picture_album">
                    <div class="picture_content">
                        <img src="/Home/images/dorsey/pic-huace1.png">
                        <h1><?php echo lang('德汇企业形象广告画册');?></h1>
                    </div>
                    <div class="picture_content">
                        <img src="/Home/images/dorsey/pic-huace2.png">
                        <h1><?php echo lang('德汇企业宣传画册');?></h1>
                    </div>
                    <div class="picture_content">
                        <img src="/Home/images/dorsey/pic-huace3.png">
                        <h1><?php echo lang('德汇运营创新“产学研一体”画册');?></h1>
                    </div>
                </div>
                <div class="content-title" style="margin-top: 30px;"><?php echo lang('德汇刊报');?>
                    <div class="newspaper_years_p">
                        <a href="javascript:void(0);" class="left" onclick="navMove(1);"></a>
                        <a href="javascript:void(0);" class="right" onclick="navMove(-1);"></a>
                        <div id="newspaper_years">
                            <div class="video-nav" style="float: left; transition: 0.2s;" id="vio_nav">
                                <?php 
                                    $yearM = date('Y');
                                    for($i = $yearM;$i>2000;$i--){
                                        if($year == $i || (!$year && $i == $yearM))echo '<a data-year="'.$i.'" href="javascript:void(0);" class="checked">'.$i.'</a>';
                                        else echo '<a data-year="'.$i.'" href="javascript:void(0);">'.$i.'</a>';
                                    }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="newspaper-content">
                    
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    var obj_width = 60;
    var nav_obj = $("#vio_nav");
    var num = nav_obj.children("a").length;
    if (num >= 9) {
        $(nav_obj.css("width", (num * obj_width - 20) + "px"));
    }
    var no_move = true;
    
    $(document).ready(function () {
        $("#vio_nav > a").click(function () {
            var obj = $(this);

            obj.addClass("checked").siblings("a").removeClass("checked");
            $.post('magazine',{year:obj.attr('data-year')},function(s){
                $('.newspaper-content').html(s).append('<div class="clear"></div>')
            })
        });
        $("#vio_nav > a.checked").click()
    });

    function navMove(type) {
        if (!no_move) {
            return false;
        }
        no_move = false;
        setTimeout(function () {
            no_move = true;
        }, 300);
        type = type == 1 ? 1 : -1;
        var obj = $("#vio_nav");
        var left = parseInt(obj.css("margin-left"));
        if (type == -1 && left <= -(num - 9) * obj_width) {
            return false;
        }
        if (type == 1 && left >= 0) {
            return false;
        }
        obj.css("margin-left", left + (obj_width * type) + "px");
    }
    
    function changeType(obj, type) {
        obj = $(obj);
        if (obj.hasClass("checked")) {
            return false;
        }
        obj.addClass("checked").siblings("a").removeClass("checked");
        if (type == 1) {
            $(".enterprise_info").hide();
            $(".culture_content").show();
        } else {
            $(".enterprise_info").show();
            $(".culture_content").hide();
        }
    }
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>