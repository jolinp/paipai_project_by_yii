<style type="text/css">
    .WA{
        width:100%
    }

    .WA td,.WA th{
        padding:4px 15px;
    }

    .WA th{
        background: #F1F1F1;
    }

</style>
<script type="text/javascript">
    <!--
    function enter_sys() {
        var url = '<?php echo $this->createUrl("/admin/admin/index");?>' + "&nick=" + $("#nick").val();
        window.location.href = url;
    }

    function reload_manager() {
        var nick = $("#nick").val();
        $.ajax({

            url: '<?php echo $this->createUrl("/admin/admin/index");?>',
            data: {search: nick, ajax: true},
            type: 'get',
            dataType: 'html',
            success: function (resp) {
                $("#login-user-manager-gridview").replaceWith($("#login-user-manager-gridview", "<div>" + resp + "</div>"));
            }
        });
    }

    function reset_init(nick) {
        if (nick == null || nick == "") {
            alert("店铺名为空,优先失败");
            return;
        } else {
            $.ajax({
                url: '<?php echo Yii::app()->request->baseUrl . "/index.php?r=/admin/admin/init"?>',
                data: {nick: nick},
                type: 'get',
                dataType: 'JSON',
                success: function (resp) {
                    alert(resp.data.msg);
                }
            });
        }
    }
    //-->
</script>
<?php
foreach(Yii::app()->user->getFlashes() as $key => $message) {
    echo '<div style="border:1px solod orange;background:yellow;line-height:24px;margin:12px 0;">' . $message . "</div>\n";
}
?>

<div style="text-align:left">
    <div class="iTxt">
        <center>
            卖家昵称：<input type="text" value="" name="LoginForm[nick]" id="nick" oninput="reload_manager()"
                        onchange="reload_manager()" onpropertychange="reload_manager()"/><input type="button"
                                                                                                value="进入系统"
                                                                                                onclick="enter_sys()"/>
        </center>
    </div>


    <div id="login-user-manager-gridview">
        <?php $i = 0;?>
        <br/>
        <table class="WA">
            <tr>
                <th></th>
                <th>卖家昵称</th>
                <th>是否允许使用系统</th>
                <th>是否允许代理账号的使用</th>
                <th>上次登录时间</th>
                <th>最近登录时间</th>
                <th>操作</th>
                <th>优化建议</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <?php $user = (object)$user; ?>
                <tr>
                    <?php
                    ?>
                    <td><?php echo ++$i;?></td>
                    <td><?php echo $user->nick;?></td>
                    <td>
                        <a href="<?php echo $this->createUrl("/admin/admin/loginSetting", array("nick" => $user->nick,"is_login"=>$user->is_login)); ?>"><?php echo $user->is_login == "TRUE" ? "允许使用" : "<span style='color:red'>不允许</span>";?></a>
                    </td>
                    <td>
<!--                        <a href="--><?php //echo $this->createUrl("/site/ProxySetting", array("nick" => $user->nick)); ?><!--">--><?php //echo $user->is_proxy_use == "TRUE" ? "允许代理账号的使用" : "<span style='color:red'>不允许代理账号的使用</span>";?><!--</a>-->
                        <?php echo $user->is_proxy_use == "TRUE" ? "允许代理账号的使用" : "<span style='color:red'>不允许代理账号的使用</span>";?>
                    </td>
                    <td><?php echo $user->last_time;?></td>
                    <td><?php echo $user->activity_time;?></td>
                    <td><?php echo CHtml::link("进入系统", array("/admin/admin/index", "nick" => $user->nick), array("target" => "_blank"));?>
                        <br/>
                        <a href="#" onclick="reset_init('<?php echo $user->nick; ?>')">重置数据</a>
                    </td>
                    <td><?php echo CHtml::link("管理", array("/admin/suggestion/admin", "Suggestion[nick]" => $user->nick), array("target" => "_blank"));?>
                    </td>
                </tr>

            <?php endforeach;?>
        </table>
    </div>
</div>



