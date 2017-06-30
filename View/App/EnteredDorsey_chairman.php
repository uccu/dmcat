<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<link rel="stylesheet" href="/Home/css/swiper.min.css">
<script type="text/javascript" src="/Home/js/swiper.jquery.min.js"></script>

<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('走进德汇');?></span>
        <form method="get" onsubmit="return false;">
            <!--input type="search" placeholder="<?php echo lang('输入关键字');?>"/-->
        </form>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('走进德汇');?> > <?php echo lang('董事长专区');?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/EnteredDorsey_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang('董事长专区');?></div>
            <?php echo langV($page,'content');?>
            <div class="content-title" style="margin-top: 30px;"><?php echo lang('董事长图片');?></div>
            <div class="chairman_info" style="padding-bottom: 0; border: none;">

                <?php 
                    foreach($chairmanPicture as $v){

                        echo '<div class="chairman_box" style="cursor:pointer">
                    <div class="table">
                        <div class="table-cell">
                            <img src="/pic/'.$v->first.'" style="height:210px">
                        </div>
                    </div>
                    <span data-image="'.$v->picArray.'">'.$v->count.' 张</span>
                    <h1>'.langV($v,'description').'</h1>
                </div>';
                    }
                
                ?>
                
                
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<!-- 预览 -->
<style>
.dialog .table img{max-height:532px}
</style>
<div class="dialog" style="display: none;">
    <div class="dialog-close" onclick="$('.dialog').hide();">✕ 关闭</div>
    <div class="chairman-images-scan">
        <div class="swiper-wrapper">
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</div>
<!-- 预览 -->
<script type="text/javascript">

    $(document).ready(function () {
        $('.chairman_box').click(function(){
            
            var data = $(this).find('span').attr("data-image");
            if (data == "") {
                return false;
            }
            /*if (data.replace(";") == data) {
                return false;
            }*/
            data = data.split(";");
            var html = '';
            $.each(data, function (i, v) {
                html += '<div class="chairman-scan-box chairman_box swiper-slide">'+
                        '<div class="table">'+
                        '<div class="table-cell">'+
                        '<img src="' + v + '">'+
                        '</div></div></div>';
            });
            $(".swiper-wrapper").html(html);
            $(".dialog").show();
            new Swiper('.chairman-images-scan', {
                effect: 'flip',
                grabCursor: true,
                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev'
            });
        });
    });
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>