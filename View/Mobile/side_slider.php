<!--footer-->
<footer>
        <p>
            <span>
                <img src="img/footerlogo.png" alt="">
                ©2013-2018
            </span>
            <span>德汇集团版权所有</span>
        </p>
        <p>
            <span>电话： 0991-7788916</span>
            <span>传真： 0991-5811770</span>
            <span>地址： 中国乌鲁木齐市奇台路658号</span>
            <!--span><a class="toCn">中文</a> <a class="toEn">English</a></span-->
            <script>
                $('.toCn').click(function(){$.post('/user/language',{l:'cn'},function(){location.reload()})})
                $('.toEn').click(function(){$.post('/user/language',{l:'en'},function(){location.reload()})})
            </script>
        </p>
    </footer>
<!--侧边栏-->
<div class="sidebar-box">
    <div class="sidebar-header">
        <img src="img/logo.png" width="80" onclick="redirect('index')">
    </div>
    <div class="sidebar-main">
        <h1>
            <span>+</span>
            走进德汇
        </h1>
        <ul>
            <li><a href="profile">集团简介</a></li>
            <li><a href="chairman">董事长专区</a></li>
            <li><a href="develop">发展历程</a></li>
            <li><a href="culture">企业文化</a></li>
            <li><a href="honor">企业荣誉</a></li>
            <li><a href="blame">社会责任</a></li>
        </ul>
    </div>
    <div class="sidebar-main">
        <h1>
            <span>+</span>
            德汇产业
        </h1>
        <ul>
            <li><a href="newWorld">德汇宝贝广场</a></li>
            <li><a href="wandaSquare">德汇万达广场</a></li>
            <li><a href="newCity">德汇特色小镇</a></li>
            <li><a href="logistics">德汇物流</a></li>
            <li><a href="finance">德汇金融</a></li>

        </ul>
    </div>
    <div class="sidebar-main">
        <h1>
            <span>+</span>
            新闻中心
        </h1>
        <ul>
            <li><a href="inNews">集团要闻</a></li>
            <li><a href="special">热点专题</a></li>
            <li><a href="media">媒体聚焦</a></li>
            <li><a href="video">视频中心</a></li>
        </ul>
    </div>
    <div class="sidebar-main">
        <h1>
            <span>+</span>
            联系我们
        </h1>
        <ul>
            <li><a href="recruit">德汇招聘</a></li>
            <li><a href="moves">招标公告</a></li>
            <li><a href="complaints">投诉与建议</a></li>
            <li><a href="legalNotices">法律声明</a></li>
        </ul>
    </div>
</div>
<script src="js/sidebar.js"></script>
