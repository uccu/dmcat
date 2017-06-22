<div id="navigation">
    <div class="w-1200">
        <div class="logo"></div>
        <div class="zh_en">
            <a href="javascript:void(0);"<?php echo $this->L->lang=='cn'?'':' class="no-check toCn"';?>>中文</a>
            <em>|</em>
            <a href="javascript:void(0);"<?php echo $this->L->lang=='en'?'':' class="no-check toEn"';?>>EN</a>
            <script>
                $('.no-check.toCn').click(function(){$.post('/user/language',{l:'cn'},function(){location.reload()})})
                $('.no-check.toEn').click(function(){$.post('/user/language',{l:'en'},function(){location.reload()})})
            </script>
        </div>
        <div class="nav">
            <div class="one-nav <if condition='$action eq Index'>checked</if>">
                <a href="/Home/Index"><?php echo lang('首页');?></a>
            </div>
            <div class="one-nav <if condition='$action eq EnteredDorsey'>checked</if>">
                <a href="javascript:void(0);"><?php echo lang('走进德汇');?></a>
                <div class="more-nav">
                    <a href="/Home/EnteredDorsey/profile"><?php echo lang('集团简介');?></a>
                    <a href="/Home/EnteredDorsey/chairman"><?php echo lang('董事长专区');?></a>
                    <a href="/Home/EnteredDorsey/develop"><?php echo lang('发展历程');?></a>
                    <a href="/Home/EnteredDorsey/culture"><?php echo lang('企业文化');?></a>
                    <a href="/Home/EnteredDorsey/honor"><?php echo lang('企业荣誉');?></a>
                    <a href="/Home/EnteredDorsey/blame"><?php echo lang('社会责任');?></a>
                </div>
            </div>
            <div class="one-nav <if condition='$action eq Domain'>checked</if>">
                <a href="javascript:void(0);"><?php echo lang('德汇产业');?></a>
                <div class="more-nav">
                    <a href="/Home/Domain/newWorld"><?php echo lang('德汇宝贝广场');?></a>
                    <a href="/Home/Domain/wandaSquare"><?php echo lang('德汇万达广场');?></a>
                    
                    <a href="/Home/Domain/logistics"><?php echo lang('德汇物流');?></a>
                    <a href="/Home/Domain/newCity"><?php echo lang('德汇特色小镇');?></a>
                    <a href="/Home/Domain/finance"><?php echo lang('德汇金融');?></a>
                    <!--a href="/Home/Domain/edu"><?php echo lang('德汇教育');?></a-->
                </div>
            </div>
            <div class="one-nav <if condition='$action eq News'>checked</if>">
                <a href="javascript:void(0);"><?php echo lang('新闻中心');?></a>
                <div class="more-nav">
                    <a href="/Home/News/inNews"><?php echo lang('集团要闻');?></a>
                    <a href="/Home/News/special"><?php echo lang('热点专题');?></a>
                    <a href="/Home/News/media"><?php echo lang('媒体聚焦');?></a>
                    <a href="/Home/News/video"><?php echo lang('视频中心');?></a>
                </div>
            </div>
            <div class="one-nav">
                <a href="javascript:void(0);"><?php echo lang('联系我们');?></a>
                <div class="more-nav">
                    <a href="/Home/CountUs/recruit"><?php echo lang('德汇招聘');?></a>
                    <a href="/Home/CountUs/moves"><?php echo lang('招标公告');?></a>
                    <a href="/Home/CountUs/complaints"><?php echo lang('投诉及建议');?></a>
                    <a href="/Home/CountUs/legalNotices"><?php echo lang('法律声明');?></a>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>