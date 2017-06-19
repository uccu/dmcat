<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>

<div class="w-1200">
    <div class="content-top">
        <span>走进德汇</span>
        <form method="get" onsubmit="return false;">
            <input type="search" placeholder="输入关键字"/>
        </form>
    </div>
    <div class="this-address">首页 > 走进德汇 > 发展历程</div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/EnteredDorsey_left.php');?>
        <div class="content-right">
            <div class="content-title">发展历程</div>
            <div class="develop-nav">
                <a href="javascript:void(0);" class="develop-nav-left" onclick="navMove(1);"></a>
                <div class="develop-nav-box">
                    <div id="develop_box">
                        <span class="checked">2017</span>
                        <span>2016</span>
                        <span>2015</span>
                        <span>2014</span>
                        <span>2013</span>
                        <span>2012</span>
                        <span>2011</span>
                        <span>2009</span>
                        <span>2008</span>
                        <span>2007</span>
                        <span>2006</span>
                        <span>2005</span>
                        <span>2004</span>
                        <span>2003</span>
                        <span>2002</span>
                        <span>2001</span>
                    </div>
                </div>
                <a href="javascript:void(0);" class="develop-nav-right" onclick="navMove(-1);"></a>
            </div>
            <div class="develop-content">
                <div class="develop-one-month">
                    <span>09月</span>
                    <div>
                        <h1>德汇集团国际级商业巨头万达集团联手建设“德汇万达广场”</h1>
                        <h2>德汇集团充分发掘自身30年商业运营管理的经验和优势，大胆创新，将原有批发市场改造升级成“主题商城”，与国际级商业巨头万达集团联手建设“德汇万达广场”，打造集时尚展示、体验娱乐、商务社交、电子商务于一体的大型智能化、体验式城市综合体。</h2>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>07月</span>
                    <div>
                        <h1>德汇亚欧新城进入规划设计阶段</h1>
                        <h2>总建筑面积达400万平方米的德汇亚欧新城进入规划设计阶段，这座世界级商贸综合体将满载丝绸之路经济带“第一国际商贸中心”的荣耀，成为中国与亚欧商贸文化的交流平台，德汇在促进中国商贸与文化走向世界的道路上正在发挥日益重要的作用。</h2>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>06月</span>
                    <div>
                        <h1>德汇新疆国际纺织品服装服饰商贸中心项目获奖</h1>
                        <h2>“德汇新疆国际纺织品服装服饰商贸中心项目”被中国纺织工业联合会、自治区纺织行办、乌鲁木齐市经信委认定为最贴近市场、最具可操作性的发展模式，被乌鲁木齐市列入“十三五”重点建设项目、市及自治区重点建设项目以及沙区招商引资先进企业。</h2>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>04月</span>
                    <div>
                        <h1>德汇开始建设“德汇宝贝广场”儿童主题商城</h1>
                        <h2>德汇开始组建儿童产业，建成新疆最大的儿童主题商城，孕婴童系列、儿童教育培训、娱乐购物主题大世界。</h2>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>02月</span>
                    <div>
                        <h1>德汇成功举办首届亚欧丝绸之路服装节</h1>
                        <h2>德汇积极推动亚欧各国文化贸易交流，成功举办首届亚欧丝绸之路服装节，从此亚欧丝绸之路服装节成为亚欧大陆影响最为广泛的民族服装交流与贸易活动之一，这一年德汇集团贸易额正式迈入百亿大关。</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    var obj_width = 84;
    var num = $("#develop_box > span").length;
    $("#develop_box").css("width", num * obj_width + "px");
    var no_move = true;

    $(document).ready(function () {
        $("#develop_box > span").click(function () {
            var obj = $(this);
            if (obj.hasClass("checked")) {
                return false;
            }
            obj.addClass("checked").siblings(".checked").removeClass("checked");
        });
    });

    function navMove(type) {
        if (!no_move) {
            return false;
        }
        no_move = false;
        setTimeout(function () {
            no_move = true;
        }, 400);
        type = type == 1 ? 1 : -1;
        var obj = $("#develop_box");
        var left = parseInt(obj.css("margin-left"));
        if (type == -1 && left <= -(num - 10) * obj_width) {
            return false;
        }
        if (type == 1 && left >= 0) {
            return false;
        }
        obj.css("margin-left", left + (obj_width * type) + "px");
    }
</script>
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>