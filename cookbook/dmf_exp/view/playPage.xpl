<!--
需要变量
LOCALVERSION
$TAGS => tag
$SOURCE : string
messages => $MessageFmt
IsMuti
-->
<!-- BEGIN: main -->
<table border='0' width='100%' cellpadding='3' cellspacing='0' >
    <tr>
        <td  valign='top'>
            <div  style='color: black; background-color: #f7f7f7; border: 1px solid #cccccc; padding: 4px;' >
                <!-- BEGIN: source -->
                <p>来源：{SOURCE}</p>
                <!-- END: source -->
                <!-- BEGIN: tagListEditable -->
                <form action='http://localhost/Acfun4p/1000' method='post'>
                    <input type='hidden' name='action' value='xestagpages' />
                    Tags:&nbsp;&nbsp;{TAGS}<input type='text' name='Tags' class='inputbox' size='6' /><input type='submit' value='追加Tag' class='inputbutton' />
                </form>
                <!-- END: tagListEditable -->
                <!-- BEGIN: tagListNormal -->
                <p>Tags:&nbsp;&nbsp;{TAGS}</p>
                <!-- END: tagListNormal -->
                <!-- BEGIN: messages -->
                <p>{MESSAGES}</p>
                <!-- END: messages -->
                <!-- BEGIN: WTF -->
                <p>{WTF#url}</p>
                <!-- END: WTF -->
                <!-- 未登录提示合并到$MessageFmt -->
            </div>
        </td>
        <td rowspan='3' bgcolor='#f7f7f7' width='950'  valign='top'>
            <div  style='font-size: small;' > 
                <p><strong>分P资料:</strong><br />还没坑完，反正没人用</p>
            </div>
            <p><strong>备注:</strong>还没坑完，反正没人用</p>
            <div class='vspace'></div>
        </td>
    </tr>
    <tr>
        <td width='950'  valign='top'>
            <hr />
            <!-- BEGIN: DanmakuBar -->
            <!-- END: DanmakuBar -->
            <div  id='flashcontent' > </div>
            <p>
                <script type="text/javascript">
                    var flashvars = {};
                    var params = {};
                    <!-- BEGIN: FlashVars -->{FLASHVARS.Name} = {FLASHVARS.Value};
                    <!-- END: FlashVars -->
                    <!-- BEGIN: PlayerLoader -->
                    swfobject.embedSWF("{URL}", "flashcontent", "{WIDTH}", "{HEIGHT}", "10.0.0","expressInstall.swf", flashvars, params);
                    <!-- END: PlayerLoader -->
                </script>
            </p>
        </td>
    </tr>
    <tr>
        <td width='950'  valign='top'>
            <hr />
            <p>切换播放器：&nbsp;&nbsp;
            <!-- BEGIN: PlayerLoaderAdmin -->
            <strong>{PLAYER.Name}</strong><a class='urllink' href='{PLAYER.SetDefaultUrl}' >&nbsp;<sup>Def</sup></a>&nbsp;&nbsp;<a class='urllink' href='{PLAYER.PlayURL}' >
            <!-- END: PlayerLoaderAdmin -->
            <!-- BEGIN: PlayerLoaderNormal -->
            <strong>{PLAYER.Name}</strong>&nbsp;&nbsp;<a class='urllink' href='{PLAYER.PlayURL}' >
            <!-- END: PlayerLoaderNormal -->
        </td>
    </tr>
</table>
</div>
</td>
</tr></table>
<!-- END: main -->