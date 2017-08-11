<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>老师/Teacher</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <!--<link rel="stylesheet" href="css/bootstrap.min.css">-->
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/index.css">
</head>

<body>
<div class="banner">
    <img src="/app/img/b1.png" alt="">
</div>
<div class="parent-main">
    <div class="parent-child personal">
        <a href="profile"></a>
        <p>个人资料<br><span>Personal Profile</span></p>
    </div>
    <div class="parent-child child">
        <a href="lists"></a>
        <p>孩子记录<br><span>Child Record</span></p>
    </div>
    <div class="parent-child leave">
        <a href="parent"></a>
        <p>家长留言<br><span>Leave Message</span></p>
    </div>
    
</div>

<div class="parent-main">
    <div class="parent-child conduct">
        <a href="apply"></a>
        <p>删除申请 <br><span>Apply</span></p>
    </div>
    <div class="parent-child scan">
        <a href="scan"></a>
        <p>扫码 <br><span>Scan</span></p>
    </div>
    <div class="parent-child message">
        <a href="post"></a>
        <p>发送通知 <br><span>Announcements</span></p>
    </div>
</div>

<script src="/app/js/main.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>

<script>

    wx.config({
        debug: false,
        appId: '<?php echo $appId;?>',
        timestamp: <?php echo $timestamp;?>,
        nonceStr: '<?php echo $nonceStr;?>',
        signature: '<?php echo $signature;?>',
        jsApiList: ['scanQRCode']
    });

    var ef = function(){
        wx.scanQRCode({
            needResult: 1,
            scanType: ["qrCode","barCode"],
            success: function (res) {

                location = '/home/attend?code=' + res.resultStr
            }
        });
    }


            
            
</script>
</body>
</html>