<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<style type="text/css">
    .profile_info p{padding-top: 0;}
    .profile_info{line-height: 30px;}
</style>
<div class="w-1200">
    <div class="content-top">
        <span>走进德汇</span>
        <form method="get" onsubmit="return false;">
            <input type="search" placeholder="输入关键字"/>
        </form>
    </div>
    <div class="this-address">首页 > 走进德汇 > 企业文化</div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/EnteredDorsey_left.php');?>
        <div class="content-right">
            <div class="content-title">企业文化
                <div class="video-nav">
                    <a href="javascript:void(0);" class="checked" onclick="changeType(this, 1);">文化体系</a>
                    <a href="javascript:void(0);" onclick="changeType(this, 2);">企业内刊</a>
                </div>
            </div>
            <div class="culture_content">
                <div class="profile_info">
                    <img src="/Home/images/dorsey/w-h-t-x.png">
                </div>
                <div class="profile_info">
                    <div style="color: #333; font-size: 14px; font-weight: 600; line-height: 50px;">一、企业愿景、使命、精神</div>
                    <p>企业愿景：致力于成为中国西部商业的领军力量</p>
                    <p>企业使命：产业兴城 实业兴邦</p>
                    <p>企业精神：德者汇天下</p>
                    <div style="color: #333; font-size: 14px; font-weight: 600; line-height: 50px;">二、经营理念</div>
                    <p>合作共赢 &nbsp; 诚信守约 &nbsp; 服务客户 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 贴近市场 &nbsp;贴近客户</p>
                    <div style="color: #333; font-size: 14px; font-weight: 600; line-height: 50px;">三、管理风格</div>
                    <p style="color: #444;">1、组织架构：</p>
                    <p>责任结构与业务结构匹配，责任中心明确，所有责任落实到岗，岗位责任落实到人；组织扁平化，减少领导岗位职数，通过信息化手段改善沟通，实现政令通畅；讲求组织原则，系统化，简约化</p>
                    <p style="color: #444;">2、流程：</p>
                    <p>关键业务流程持续优化，沉淀业务经验，打造核心竞争力，建立高绩效组织</p>
                    <p style="color: #444;">3、制度：</p>
                    <p>制度面前人人平等，关键节点严格控制</p>
                    <p style="color: #444;">4、知识与信息：</p>
                    <p>制度面前人人平等，关键节点严格控制</p>
                    <p style="color: #444;">5、会议：</p>
                    <p> 会前有准备，会中有记录，会后有落实；有话则长，无话则短，尊重别人的时间价值；每个人都有讲话的权利；杜绝会上不说会下乱说</p>
                    <div style="color: #333; font-size: 14px; font-weight: 600; line-height: 50px;">四、用人哲学</div>
                    <p style="color: #444;"> 1、人才观：</p>
                    <p>英雄不问出处，广纳贤才，才尽其用；能者上，庸者下；品格高于学历；责任心重于技能；精湛的服务能力来自于专业</p>
                    <p style="color: #444;"> 2、团队：</p>
                    <p>团队合作；团队的成功才是个人的成功；低绩效团队中没有真正成功的个人；资源向高绩效团队/组织倾斜</p>
                    <p style="color: #444;"> 3、评价与激励：</p>
                    <p>依靠KPI正式评价，杜绝小报告，禁止随意的说长道短；业绩说话，公正评价，奖罚分明；及时奖励；将股权、培训和发展机会作为奖励</p>
                    <p style="color: #444;"> 4、学习与发展：</p>
                    <p>倡导终生学习；自我学习与培训发展相结合</p>
                </div>
            </div>
            <div class="enterprise_info" style="display: none;">
                <div class="picture_album">
                    <div class="picture_content">
                        <img src="/Home/images/dorsey/pic-huace1.png">
                        <h1>德汇企业形象广告画册</h1>
                    </div>
                    <div class="picture_content">
                        <img src="/Home/images/dorsey/pic-huace2.png">
                        <h1>德汇企业宣传画册</h1>
                    </div>
                    <div class="picture_content">
                        <img src="/Home/images/dorsey/pic-huace3.png">
                        <h1>德汇运营创新“产学研一体”画册</h1>
                    </div>
                </div>
                <div class="content-title" style="margin-top: 30px;">德汇刊报
                    <div class="newspaper_years_p">
                        <a href="javascript:void(0);" class="left" onclick="navMove(1);"></a>
                        <a href="javascript:void(0);" class="right" onclick="navMove(-1);"></a>
                        <div id="newspaper_years">
                            <div class="video-nav" style="float: left; transition: 0.2s;" id="vio_nav">
                                <a href="javascript:void(0);" class="checked">2017</a>
                                <a href="javascript:void(0);">2016</a>
                                <a href="javascript:void(0);">2015</a>
                                <a href="javascript:void(0);">2014</a>
                                <a href="javascript:void(0);">2013</a>
                                <a href="javascript:void(0);">2012</a>
                                <a href="javascript:void(0);">2011</a>
                                <a href="javascript:void(0);">2010</a>
                                <a href="javascript:void(0);">2009</a>
                                <a href="javascript:void(0);">2008</a>
                                <a href="javascript:void(0);">2007</a>
                                <a href="javascript:void(0);">2006</a>
                                <a href="javascript:void(0);">2005</a>
                                <a href="javascript:void(0);">2004</a>
                                <a href="javascript:void(0);">2003</a>
                                <a href="javascript:void(0);">2002</a>
                                <a href="javascript:void(0);">2001</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="newspaper-content">
                    <div class="newspaper-one">
                        <img src="/Home/images/dorsey/month-1.png">
                        <h1>德汇人 6月报刊</h1>
                        <h2>总第125期 &nbsp; 本期4版 &nbsp; 2015年6月</h2>
                        <h3>“艾德莱斯炫昆仑” 启动</h3>
                        <h4>从苍茫的昆仑山下， 到时尚前卫的上海滩， 一条艾德莱斯炫带正从新疆舞动到上海。6 月 23 日，“艾德莱斯炫昆仑援疆万里行暨创意产品上海见面会” 盛大启幕。</h4>
                    </div>
                    <div class="newspaper-one">
                        <img src="/Home/images/dorsey/month-2.png">
                        <h1>德汇人 5月报刊</h1>
                        <h2>总第124期 &nbsp; 本期4版 &nbsp; 2015年5月</h2>
                        <h3>市委副书记、 市长伊力哈木·沙比尔一行莅临德汇</h3>
                        <h4>5 月 20 日， 市委副书记、 市长伊力哈木·沙比尔、 市政府秘书长、 办公厅主任肖勇、 沙依巴克区区长斯地克·牙生等一行莅临德汇集团检查指导工作。</h4>
                    </div>
                    <div class="newspaper-one">
                        <img src="/Home/images/dorsey/month-3.png">
                        <h1>德汇人 4月报刊</h1>
                        <h2>总第123期 &nbsp; 本期4版 &nbsp; 2015年6月</h2>
                        <h3>“艾德莱斯炫昆仑” 启动</h3>
                        <h4>从苍茫的昆仑山下， 到时尚前卫的上海滩， 一条艾德莱斯炫带正从新疆舞动到上海。6 月 23 日，“艾德莱斯炫昆仑援疆万里行暨创意产品上海见面会” 盛大启幕。</h4>
                    </div>
                    <div class="newspaper-one">
                        <img src="/Home/images/dorsey/month-4.png">
                        <h1>德汇人 3月报刊</h1>
                        <h2>总第122期 &nbsp; 本期4版 &nbsp; 2015年5月</h2>
                        <h3>市委副书记、 市长伊力哈木·沙比尔一行莅临德汇</h3>
                        <h4>5 月 20 日， 市委副书记、 市长伊力哈木·沙比尔、 市政府秘书长、 办公厅主任肖勇、 沙依巴克区区长斯地克·牙生等一行莅临德汇集团检查指导工作。</h4>
                    </div>
                    <div class="newspaper-one">
                        <img src="/Home/images/dorsey/month-5.png">
                        <h1>德汇人 2月报刊</h1>
                        <h2>总第121期 &nbsp; 本期4版 &nbsp; 2015年6月</h2>
                        <h3>“艾德莱斯炫昆仑” 启动</h3>
                        <h4>从苍茫的昆仑山下， 到时尚前卫的上海滩， 一条艾德莱斯炫带正从新疆舞动到上海。6 月 23 日，“艾德莱斯炫昆仑援疆万里行暨创意产品上海见面会” 盛大启幕。</h4>
                    </div>
                    <div class="newspaper-one">
                        <img src="/Home/images/dorsey/month-6.png">
                        <h1>德汇人 1月报刊</h1>
                        <h2>总第120期 &nbsp; 本期4版 &nbsp; 2015年5月</h2>
                        <h3>市委副书记、 市长伊力哈木·沙比尔一行莅临德汇</h3>
                        <h4>5 月 20 日， 市委副书记、 市长伊力哈木·沙比尔、 市政府秘书长、 办公厅主任肖勇、 沙依巴克区区长斯地克·牙生等一行莅临德汇集团检查指导工作。</h4>
                    </div>
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
            if (obj.hasClass("checked")) {
                return false;
            }
            obj.addClass("checked").siblings("a").removeClass("checked");
        });
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