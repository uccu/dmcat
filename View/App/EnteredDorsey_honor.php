<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<style type="text/css">
    .develop-one-month div h1{font-size: 14px; color: #676767;}
</style>
<div class="w-1200">
    <div class="content-top">
        <span>走进德汇</span>
        <form method="get" onsubmit="return false;">
            <input type="search" placeholder="输入关键字"/>
        </form>
    </div>
    <div class="this-address">首页 > 走进德汇 > 企业荣誉</div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/EnteredDorsey_left.php');?>
        <div class="content-right">
            <div class="content-title">企业荣誉</div>
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
                    <span>11月</span>
                    <div>
                        <h1>德汇集团董事长钱金耐在自治区工商联（总商会）十届委员会会议上当选副主席</h1>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>08月</span>
                    <div>
                        <h1>德汇集团董事长钱金耐被评为全国诚信模范人物</h1>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>06月</span>
                    <div>
                        <h1>德汇集团总裁张献开荣获乌市“创先争优优秀共产党员”称号</h1>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>04月</span>
                    <div>
                        <h1>集团董事长钱金耐荣获全国十大道德模范</h1>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>02月</span>
                    <div>
                        <h1>德汇集团荣获“新疆著名商标”称号</h1>
                    </div>
                </div>
                <div class="develop-one-month">
                    <span>01月</span>
                    <div>
                        <h1>德汇集团荣获品牌中国（商业地产类）金谱奖</h1>
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