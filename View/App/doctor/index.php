<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>医生/Doctor</title>
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
    <div class="parent-child scan">
        <a href="javascript:ef();"></a>
        <p>扫码 <br><span>scan</span></p>
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