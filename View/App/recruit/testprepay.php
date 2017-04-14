<html>
<head>
<script src="js/jquery-1.8.3.min.js"></script>
</head>
<body>


<script>

function onBridgeReady(){

    $.post('/wc/prepay',{out_trade_no:"<?php echo $id;?>"},function(d){

        WeixinJSBridge.invoke(
            'getBrandWCPayRequest', d.data,
            function(res){     
                if(res.err_msg == "get_brand_wcpay_request:ok" ) {}     // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。 
            }
        ); 
    })
   
}
if (typeof WeixinJSBridge == "undefined"){
   if( document.addEventListener ){
       document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
   }else if (document.attachEvent){
       document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
       document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
   }
}else{
   onBridgeReady();
}


</script>

</body>

</html>