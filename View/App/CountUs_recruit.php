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
        <div class="content-right" style="position: relative;">
            <div class="content-title">德汇人才观</div>
            <div class="d-h-r-c-g-r">为每位员工提供施展才华的舞台和机会，通过梯队建设体系保持人才发展优势！</div>
            <div class="d-h-r-c-g-c">
                公司鼓励员工专精所长，为不同类型人员提供平等晋升机会，给予员工充分的职业发展空间。<br/>
                公司根据各岗位工作性质的不同，设立不同的职业发展通道，使从事不同岗位工作的员工均有可持续发展的职业发展通道。
            </div>
            <div class="content-title" style="margin-top: 20px;">德汇人才培养机制</div>
            <div class="d-h-r-c-g-c">
                每一岗位对应一种职业发展通道，随着员工技能与绩效的提升，可以在各自的通道内获得平等的晋升机会。<br/>
                考虑公司发展需要、员工个人实际情况及职业兴趣，员工在不同通道之间有转换机会，但转换必须符合各职系相应职务任职条件，并按公司相关制度执行。如果员工的岗位发生变动，其级别根据新岗位确定。
            </div>
            <img id="rc_people" src="/Home/images/people.png"/>
            <div class="content-title" style="margin-top: 40px;">招聘职位
                <div class="video-nav">

                    <?php foreach($recruitType as $k=>$v){?>

                        <a href="javascript:void(0);" data-id="<?php echo $v->id;?>"<?php echo $v->id==$typez?' class="checked"':'';?>><?php echo langV($v,'name');?></a>

                    <?php }?>
                </div>
            </div>
            <div class="recruit-info-content">
                <table id="recruit-info-table">
                    <thead>
                        <tr>
                            <td><?php echo lang('职位名称');?></td>
                            <td><?php echo lang('工作地点');?></td>
                            <td><?php echo lang('招聘人数');?></td>
                            <td><?php echo lang('工作经验');?></td>
                            <td style="width: 100px;"><?php echo lang('发布时间');?></td>
                            <td style="width: 40px;">&nbsp;</td>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach($recruit as $v){?>
                        <tr>
                            <td<?php echo $v->top?' class="important"':'';?>><?php echo langV($v,'name');?></td>
                            <td><?php echo langV($v,'address');?></td>
                            <td><?php echo $v->num;?>人</td>
                            <td><?php echo langV($v,'experience');?></td>
                            <td><?php echo langV($v,'time');?></td>
                            <td><i class="icon"></i></td>
                        </tr>
                        <tr style="display: none;">
                            <td colspan="6">
                                <div class="recruit-one-info">
                                    <h1>学历：<span class="color-black"><?php echo langV($v,'edu');?></span><em></em>工作类型：<span class="color-black"><?php echo langV($v,'typein');?></span>
                                        <div class="color-black">注：投递简历时请注明所申请的职位<a href="mailto:test@doowin.com">立即申请</a></div>
                                    </h1>
                                    <div class="info">
                                        <?php echo langV($v,'content');?>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <?php }?>

                        
                    </tbody>
                </table>

                <div id="page_content">
                    <?php echo $this->getPageLink($page,$max,'',$limit);?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".video-nav a").click(function () {
            var obj = $(this);
            $.post('/user/recruit_type',{l:obj.attr('data-id')},function(){location.reload()})
        });
        $("#recruit-info-table td .icon").click(function () {
            var obj = $(this);
            var object = obj.parents("tr").next("tr");
            if (obj.hasClass("show")) {
                obj.removeClass("show");
                object.hide();
            } else {
                obj.addClass("show");
                object.show();
            }
        });
    });
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>