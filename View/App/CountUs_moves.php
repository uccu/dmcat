<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('联系我们');?></span>
        <form method="get" onsubmit="return false;">
            <input type="search" placeholder="<?php echo lang('输入关键字');?>"/>
        </form>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('联系我们');?> > <?php echo lang($name);?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/CountUs_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang($name);?></div>
            <?php foreach($list as $info){?>

                <div class="newsContent" data-id="<?php echo $info->id;?>"><em>●</em><h3>
                    <?php if($info->top){

                        echo '【'.lang('顶置').'】';
                    }?>
                    <?php echo langV($info,'title');?></h3><span><?php echo date('Y-m-d',$info->create_time);?></span></div>
            <?php }?>
            

            <div id="page_content">
                <?php echo $this->getPageLink($page,$max,'',$limit);?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".newsContent").click(function () {
            var id = $(this).attr('data-id');
            if (!id) {
                return false;
            }
            window.open("/Home/CountUs/movesInfo?id=" + id);
        });
    });
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>