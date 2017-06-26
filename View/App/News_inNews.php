<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('新闻中心');?></span>
        <form id="search" method="post" onsubmit="return false;">
            <select name="type">
                <option value="1"><?php echo lang('集团要闻');?></option>
                <option value="2"><?php echo lang('热点专题');?></option>
                <option value="3"><?php echo lang('媒体聚焦');?></option>
                <option value="4"><?php echo lang('视频中心');?></option>
            </select>
            <input type="search" name="search" value="" placeholder="<?php echo lang('输入关键字');?>"/>
        </form>
        <script>
            $('form#search').submit(function(){
                $.post('/news/search',$(this).serialize(),function(d){
                    if(d.url)location = d.url
                },'json')
                return false
            })
        </script>
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('新闻中心');?> > <?php echo lang($name);?></div>
    <div class="main-content" style="min-height:700px">
        <?php include(VIEW_ROOT.'App/News_left.php');?>
        <div class="content-right">
            <div class="content-title"><?php echo lang($name);?></div>
            <?php if($first){?>
                    <div class="newsContent first" data-id="<?php echo $first->id;?>">
                        <img src="/pic/<?php echo $first->pic;?>">
                        <h1><?php echo langV($first,'title');?></h1>
                        <h2><?php echo langV($first,'description',140);?></h2>
                        <span><?php echo date('Y-m-d',$first->create_time);?></span>
                    </div>
            <?php }?>

            <?php foreach($list as $i=>$info){if($i == -1){?>

                    
            <?php }else{?>
                    <div class="newsContent" data-id="<?php echo $info->id;?>">
                        <em>●</em>
                        <h3><?php echo langV($info,'title');?></h3>
                        <span><?php echo date('m-d',$info->create_time);?></span></div>

            <?php }}?>
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
            window.open("/Home/News/newsInfo?id=" + id);
        });
    });
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>