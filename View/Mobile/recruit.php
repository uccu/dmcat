<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta name="apple-themes-web-app-capable" content="yes">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="format-detection" content="telephone=no">
    <title>联系我们</title>
    <link rel="stylesheet" href="css/swiper.min.css">
    <link href="css/reset.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script src="js/swiper.jquery.min.js"></script>
</head>
<body>
<header>
    <div class="header-left" onclick="getBackShuaXin()"></div>
    <div class="header-center"></div>
    <div class="header-right"></div>
</header>
<p class="fenge"></p>
<div class="group-banner">
    <img src="img/group/banner1.png">
</div>
<div class="chairman-search">
    <!--input type="search" placeholder="输入关键字"-->
</div>
<div class="main-box talent-box">
    <h1>德汇人才观</h1>
    <h3>DOOWIN TALENT</h3>
    <p>为每位员工提供施展才华的舞台和机会，通过梯队建设体系保持人才发展优势！</p>
    <span>
        公司鼓励员工专精所长，为不同类型人员提供平等晋升机会，给予员工充分的职业发展空间。<br>
        公司根据各岗位工作性质的不同，设立不同的职业发展通道，使从事不同岗位工作的员工均有可持续发展的职业发展通道。
    </span>
    <h4>德汇人才培养机制</h4>
    <span>每一岗位对应一种职业发展通道，随着员工技能与绩效的提升，可以在各自的通道内获得平等的晋升机会。<br>
考虑公司发展需要、员工个人实际情况及职业兴趣，员工在不同通道之间有转换机会，但转换必须符合各职系相应职务任职条件，并按公司相关制度执行。如果员工的岗位发生变动，其级别根据新岗位确定。</span>
</div>
<div class="main-box talent-box" style="margin-bottom:0">
    <h1>招聘职位</h1>
    <h3>POSITION VACANT</h3>
    <div class="video-nav" style="overflow-x: scroll; overflow-y: hidden;">
        <div class="video-nav-top">
            <?php foreach($recruitType as $k=>$v){?>

                        <a href="javascript:void(0);" data-id="<?php echo $v->id;?>"<?php echo $v->id==$typez?' class="active"':'';?>><?php echo langV($v,'name');?></a>

                    <?php }?>
        </div>
    </div>
</div>
<style>
thead td{font-size:10px}
</style>
<div class="recruit-info-content">
    <table id="recruit-info-table">
        <thead>
        <tr>
            <td>职位名称</td>
            <td>工作地点</td>
            <td>招聘人数</td>
            <td>工作经验</td>
            <td></td>
            <td style="width: 0.2rem;">&nbsp;</td>
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
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".video-nav-top a").click(function () {
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
<?php include('side_slider.php');?>

</body>
</html>