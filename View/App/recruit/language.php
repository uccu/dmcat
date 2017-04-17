<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $this->lang->recruit->fill_in;?></title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="css/reset.css">
    <!--<link rel="stylesheet" href="css/public.css">-->
    <link rel="stylesheet" href="css/kszl.css">
</head>
<body>
    <div class="zl-main">
        <a href="#" class="cn">中文</a>
        <a href="#" class="en">English</a>
    </div>
<script src="js/main.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script>

            $('.cn').click(function(){
                $.post('/user/language',{l:'cn'},function(){location="view_exam_list"})
            });
            $('.en').click(function(){
                $.post('/user/language',{l:'en'},function(){location="view_exam_list"})
            })
    


</script>
</body>
</html>