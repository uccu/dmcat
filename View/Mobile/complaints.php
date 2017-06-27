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
    <link rel="stylesheet" href="/Home/css/calendar.css">
    <script type="text/javascript" src="/Home/js/calendar.js"></script>
</head>
<body>
<header>
    <div class="header-left" onclick="getBackShuaXin()"></div>
    <div class="header-center"></div>
    <div class="header-right"></div>
</header>
<p class="fenge"></p>
<div class="main-box advice-box" style="margin-top: 0">
    <h1>投诉及建议</h1>
    <h3>ADVICE</h3>
    <p>
        <em>"聆听客户声音，真诚服务客户"</em>
        是德惠集团一贯的追求。为持续提升服务品质，德汇集团成立了专职部门负责客户服务，受理各类投诉与建议、监督各公司客户服务质量，以实现为客户创造价值，与客户合作共赢的服务目标。
    </p>
    <p>
        您如果对我们的产品服务不满意，或者有好的建议，请您填写一下表格，我们的客服专员将会与您联系，为您提供反馈信息并协商解决方案。
    </p>
</div>
<div class="content-title">
    <i id="icon-complaints"></i>
    在线投诉
    <span id="complaints-assess">注：带<em>“*”</em> 为必填项，必填项未填写完整则无法提交</span>
</div>
<form id="complaints-form" method="post" onsubmit="return false;">
    <table id="complaints-table">
        <tbody>
        
        <tr>
            <td style="width:2.5rem;font-size:0.15rem">发生时间：</td><td><input name="date" type="text" id="select_data" readonly="readonly" placeholder="请选择"/></td>
        </tr>
        <tr>
            <td valign="top" style="width:2rem;font-size:0.15rem"><em>*</em>内容：</td>
            <td colspan="8">
                <textarea placeholder="请输入内容说明" name="content" ></textarea>
            </td>
        </tr>
        <tr>
            <td valign="top" style="width:2rem;font-size:0.15rem"><em>*</em>要求：</td>
            <td colspan="8">
                <textarea placeholder="请输入您的要求" name="requires"></textarea>
            </td>
        </tr>
        <tr>
            <td style="width:2rem;font-size:0.15rem"><em>*</em>姓名：</td><td><input name="name" type="text" placeholder="您的真实姓名"/></td>
            <td style="width:2rem;font-size:0.15rem"><em>*</em>称谓：</td><td><select><option name="sex" value="">请选择</option><option value="1">先生</option><option value="2">女士</option></select></td>
        </tr>
        <tr>
            <td style="width:2rem;font-size:0.15rem"><em>*</em>手机号：</td><td><input name="mobile" type="number" placeholder="您的手机号码"/></td>
            <td style="width:2rem;font-size:0.15rem">联系电话：</td><td><input type="number" name="phone" placeholder="座机或其他联系电话"/></td>
        </tr>
        
        
        </tbody>
    </table>
</form>
<div class="submit-button">
    <a href="javascript:void(0);" class="upload_btn submit_btn">提 交</a><a href="javascript:void(0);" class="upload_btn reset_btn" onclick="resetForm();">重 置</a>
</div>
<script type="text/javascript">
    var date = get_date();
    $("#select_data").Calendar({toolbar:true,zIndex:999, range:["", date]});
    function resetForm() {
        document.getElementById("complaints-form").reset();
    }
    $('.submit_btn').click(function(){
        
            $.post('send',$('#complaints-form').serializeArray(),function(d){
                if(d.code==200)alert('成功！')
                else alert('失败！')
            },'json')


            return false;
        
    })

    function get_date(){
        var time = new Date();
        var year = time.getFullYear();
        var month = parseInt(time.getMonth()) + 1;
        var day = time.getDate();
        month = (month>=10)?month:"0"+month;
        day = (day>=10)?day:"0"+day;
        return year+'-'+month+'-'+day;
    }
</script>
<?php include('side_slider.php');?>

</body>
</html>