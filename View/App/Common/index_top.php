<div id="index_top" class="w-1200">
    <?php echo lang('德汇集团旗下网站');?>：
    <a href="javascript:void(0);"><?php echo lang('德汇集团');?></a> <em>|</em>
    <a href="http://www.doowinedu.com/" target="_blank"><?php echo lang('德汇教育');?></a> <em>|</em>
    <a href="javascript:void(0);" target="_blank"><?php echo lang('德汇金融');?></a> <em></em> <em></em> <em></em>
    <a href="/Home/CountUs/recruit"><?php echo lang('加入我们');?></a> <em>|</em>
    <a href="/Home/CountUs/complaints"><?php echo lang('联系我们');?></a>
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

        })
    </script>
</div>