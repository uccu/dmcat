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
            <div class="moves-title">
                <h1><?php echo langV($info,'title')?></h1>
                <span><?php echo lang('发布时间')?>：<?php echo date('Y-m-d',$info->create_time);?></span>
            </div>
            <div class="moves-info-content">
                <?php echo langV($info,'content')?>
            </div>
            <div class="downloadFile">
                <h1>附件下载：</h1>
                <h2>附件名：1、潜在新增供方登记表 &nbsp;&nbsp; 2、滑雪乐园冷库板供应及安装供方资格预审文件</h2>
                <h3>点击下载：<a href="javascript:void(0);">附件1潜在新增供方登记表</a><br/><a href="javascript:void(0);">滑雪乐园冷库板供应及安装供方资格预审文件</a></h3>
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
            window.open("/Home/CountUs/movesInfo/id/" + id);
        });
    });
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>