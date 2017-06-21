
<?php include(VIEW_ROOT.'App/Common/header.php');?>
<?php include(VIEW_ROOT.'App/Common/navigation.php');?>
<link rel="stylesheet" href="/Home/css/calendar.css">
<script type="text/javascript" src="/Home/js/calendar.js"></script>
<style type="text/css">
    .culture_content p{color: #787878; font-size: 12px; line-height: 20px; padding: 10px 0;}
</style>
<div class="w-1200">
    <div class="content-top">
        <span><?php echo lang('联系我们');?></span>
        
    </div>
    <div class="this-address"><?php echo lang('首页');?> > <?php echo lang('联系我们');?> > <?php echo lang($name);?></div>
    <div class="main-content">
        <?php include(VIEW_ROOT.'App/CountUs_left.php');?>
        <div class="content-right">
            <div class="content-title" style="font-weight: 600;"><?php echo lang($name);?></div>
            <div class="culture_content">
                <p><em style="color: #DB0B2D;">“聆听客户声音，真诚服务客户”</em>是德汇集团一贯的追求。为持续提升服务品质，德汇集团成立了专职部门负责客户服务，受理各类投诉与建议、监督各公司客户服务质量，以实现为客户创造价值，与客户合作共赢的服务目标。</p>
                <p>您如果对我们的产品和服务不满意，或者有好的建议，请您填写以下表格，我们的客服专员将会与您联系，为您提供反馈信息并协商解决方案。</p>
            </div>
            <div class="content-title" style="font-weight: 600; margin-top: 30px;"><i id="icon-complaints"></i>在线投诉<span id="complaints-assess">注：带<em>“*”</em> 为必填项，必填项未填写完整则无法提交</span></div>
            <form id="complaints-form" method="post" onsubmit="return false;">
                <table id="complaints-table">
                    <tr>
                        <td style="width: 75px;"><em>*</em>类别：</td><td><select><option value="">请选择</option></select></td>
                        <td style="width: 75px;"><em>*</em>业态：</td><td><select><option value="">请选择</option></select></td>
                        <td style="width: 75px;"><em>*</em>类型：</td><td style="width: 200px;"><select><option value="">请选择</option></select></td>
                    </tr>
                    <tr>
                        <td><em></em>省份：</td><td><select><option value="">请选择</option></select></td>
                        <td><em></em>城市：</td><td><select><option value="">请选择</option></select></td>
                        <td><em>*</em>门店：</td><td><select><option value="">请选择</option></select></td>
                    </tr>
                    <tr>
                        <td>发生时间：</td><td><input type="text" id="select_data" readonly="readonly" placeholder="请选择"/></td>
                        <td>&nbsp;</td><td>&nbsp;</td>
                        <td>&nbsp;</td><td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="top" style="padding-top: 12px;"><em>*</em>内容：</td>
                        <td colspan="5">
                            <textarea placeholder="请输入内容说明" style="height: 80px;"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" style="padding-top: 12px;"><em>*</em>要求：</td>
                        <td colspan="5">
                            <textarea placeholder="请输入您的要求" style="height: 20px;"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td><em>*</em>姓名：</td><td><input type="text" placeholder="您的真实姓名"/></td>
                        <td><em>*</em>称谓：</td><td><select><option value="">请选择</option><option value="1">先生</option><option value="2">女士</option></select></td>
                        <td>&nbsp;</td><td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><em>*</em>手机号：</td><td><input type="number" placeholder="您的手机号码"/></td>
                        <td>联系电话：</td><td><input type="number" placeholder="座机或其他联系电话"/></td>
                        <td>&nbsp;</td><td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>附件：</td><td colspan="5"><a href="javascript:void(0);" class="upload_btn">上传文件</a></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td><td colspan="5" style="color: #909090; font-size: 12px;">*注：只能上传1个附件，大小不超过5M。多个文件请压缩成一个文件后上传。</td>
                    </tr>
                    <tr>
                        <td><em>*</em>验证码：</td><td colspan="5"><input type="number" placeholder="请输入" style="width: 100px;"/><img class="captcha" style="cursor:pointer" src="/user/captcha"></td>
                        <script>
                            $('.captcha').click(function(){
                                $(this)[0].src = '/user/captcha?e='+Math.random();
                            })
                        </script>
                    </tr>
                    <tr>
                        <td>&nbsp;</td><td colspan="5"><a href="javascript:void(0);" class="upload_btn submit_btn">提 交</a><a href="javascript:void(0);" class="upload_btn reset_btn" onclick="resetForm();">重 置</a></td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    var date = get_date();
    $("#select_data").Calendar({toolbar:true,zIndex:999, range:["", date]});
    function resetForm() {
        document.getElementById("complaints-form").reset();
    }

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
<?php include(VIEW_ROOT.'App/Common/common_footer.php');?>
<?php include(VIEW_ROOT.'App/Common/footer.php');?>