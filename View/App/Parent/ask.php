<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>请假/Ask forleave</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/askforleave.css">
    <script type="text/javascript" src="/app/jeDate/jedate.js"></script>
</head>

<body>
<div class="proposer">
    <p><span>申请人</span><span>proposer</span></p>
    <input type="text" class="pro">
</div>
<div class="proposer">
    <p><span>请假时间</span><span>start time</span></p>
    <p class="datep" id="stardatep"><input class="datainp" id="dateinfo" type="text" placeholder="请选择请假时间"  readonly></p>
</div>
<!--<div class="proposer">-->
    <!--<p><span>结束时间</span><span>end time</span></p>-->
    <!--<p class="datep" id="enddatep"><input class="datainp" id="enddateinfo" type="text" placeholder="请选择请假结束时间"  readonly></p>-->
<!--</div>-->
<div class="proposer">
    <p><span>请假类型</span><span>leave type</span></p>
    <select name="" id="">
        <option value="1">病假/sick leave</option>
        <option value="2">事假/thing leave</option>
    </select>
</div>
<div class="proposer" style="height: 2.4rem;">
    <p class="proposerLast"><span>请假事由</span><span style="width: 1.9rem;">reason for leave</span></p>
    <textarea name="" class="reason"></textarea>
</div>
<p class="tishi">温馨提示：请假至少需要提前8小时</p>
<a href="javascript:void(0)" class="submitask">提交/refer</a>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.7.1.min.js"></script>
<script>
    jeDate({
        dateCell:"#dateinfo",
        format:"YYYY-MM-DD",
//        isinitVal:true,
        isTime:false, //isClear:false,
        isClear: false, //是否显示清空
        minDate: jeDate.now(0),
    })
//    jeDate({
//        dateCell:"#enddateinfo",
//        format:"YYYY-MM-DD",
////        isinitVal:true,
//        isTime:false, //isClear:false,
//        isClear: false, //是否显示清空
//        okfun:function(val){alert(val)}
//
//    })

    $(function() {
        $(".submitask").click(function(){
            var pro=$(".pro").val();
            var startime = $("#dateinfo").val();
//        var endtime = $("#enddateinfo").val();
//            var leavetype = $('select').val();
            console.info($('select').val())
            var reason = $(".reason").val();
            var type = ''
            if ($('select').val() == 1){
                type = "病假/sick leave"
            }else{
                type = "事假/thing leave"
            }
            $.ajax({
                url: "/student/ask_leave",
                type: "get",
                data: {
                    student_id:2,
//                    id:2,
                    proposer:pro,
                    date:startime,
                    type:type,
                    content:reason
                },
                dataType: "json",
                cache: false,
                success: function (e) {
                    if (e.code == 200) {
                        alert("您已提交请假申请");
                        location = 'index'
                    }else{
                        alert(e.message);
                    }
                }
            })

        })

    })
</script>
</body>
</html>