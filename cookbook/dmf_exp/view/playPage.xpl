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
            </div>
        </td>
        <td rowspan='3' bgcolor='#f7f7f7' width='950'  valign='top'>
            <div  style='font-size: small;' > 
                <p><strong>分P:</strong><br />
                <!-- BEGIN: PARTDATA -->
                {PARTTEXT}
                <!-- END: PARTDATA -->
             </p></div>
            <p><strong>备注:</strong>
            <!-- BEGIN: DESC -->
            {DESCTEXT}
            <!-- END: DESC -->
            </p><div class='vspace'></div>
        </td>
    </tr>
    <tr>
        <td width='950'  valign='top'>
            <hr />
            <!-- BEGIN: DanmakuBar -->
            <!-- BEGIN: Upload -->
            <form action='/poolop/post/{GROUP}/{DANMAKUID}' class='DanmakuBar' enctype='multipart/form-data' method='post'>
                <input name='uploadfile' type='file' />弹幕池:<select class='inputbox' name='Pool'>
                    <option value='Static'>静态</option>
                    <option value='Dynamic'>动态</option>
                </select>追加:<input name='Append' type='checkbox' value='true' /><input class='inputbutton' name='post' type='submit' value='上传' />
            </form>&nbsp;&nbsp;
            <!-- END: Upload -->
            <!-- BEGIN: Download -->
            <form action='/poolop/loadxml/{GROUP}/{DANMAKUID}' class='DanmakuBar' method='get'>
                下载格式：
                <select class='inputbox' name='format'>
                    <!-- BEGIN: Format -->
                    <option value='{FORMAT}'>{FORMAT}</option>
                    <!-- END: Format -->
                </select>附件：<input checked='checked' name='attach' type='checkbox' value='true' /><input class='inputbutton' type='submit' value='下载' />
            </form>&nbsp;&nbsp;
            <!-- END: Download -->
            <!-- BEGIN: NewLine--> <br /> <!-- END: NewLine-->
            <!-- BEGIN: DynamicPool -->
            <span style='color: black; background-color: #f7f7f7; border: 1px solid #cccccc; padding: 4px; line-height: 2em;'>
                <a class='urllink' href='/poolop/validate/{GROUP}/{DANMAKUID}/dynamic' style='color: black'>验证动态池</a>&nbsp;&nbsp;
                <a class='wikilink' href='/DMR/{SUID}{DANMAKUID}?action=edit' style='color: black'>动态池编辑</a>&nbsp;&nbsp;
            </span>
            <!-- END: DynamicPool -->
            <!-- BEGIN: PageOperation -->
            <span style='color: black; background-color: #f7f7f7; border: 1px solid #cccccc; padding: 4px; line-height: 2em;'>
                <a class='wikilink' href='?action=edit' style='color: black'>编辑Part</a>&nbsp;&nbsp;
            </span>
            <!-- END: PageOperation -->
            <!-- BEGIN: PoolOperation -->
            <span style='color: black; background-color: #f7f7f7; border: 1px solid #cccccc; padding: 4px; line-height: 2em;'>
                清空弹幕池： 
                    <a class='urllink' href='/poolop/clear/{GROUP}/{DANMAKUID}/static' style='color: black'>静态</a>&nbsp;
                    <a class='urllink' href='/poolop/clear/{GROUP}/{DANMAKUID}/dynamic' style='color: black'>动态</a>&nbsp;
                    <a class='urllink' href='/poolop/clear/{GROUP}/{DANMAKUID}/all' style='color: black'>双杀</a>&nbsp;&nbsp;&nbsp;
                移动弹幕池： 
                    <a class='urllink' href='/poolop/move/{GROUP}/{DANMAKUID}/static/dynamic' style='color: black'>S-D</a>&nbsp;
                    <a class='urllink' href='/poolop/move/{GROUP}/{DANMAKUID}/dynamic/static' style='color: black'>D-S</a>&nbsp;
            </span>
            <!-- END: PoolOperation -->
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
            <!-- BEGIN: PlayerLoaderCurrent -->
            <strong>{NAME}</strong>&nbsp;&nbsp;
            <!-- END: PlayerLoaderCurrent -->
            <!-- BEGIN: PlayerLoaderAdmin -->
            <a class='urllink' href='{PLAYER.URL}'>{NAME}</a><a class='urllink' href='{PLAYER.SetDefaultUrl}'>&nbsp;<sup>Def</sup></a>&nbsp;&nbsp;
            <!-- END: PlayerLoaderAdmin -->
            <!-- BEGIN: PlayerLoaderNormal -->
            <a class='urllink' href='{PLAYER.URL}'>{NAME}</a>&nbsp;&nbsp;
            <!-- END: PlayerLoaderNormal -->
        </td>
    </tr>
</table>
</div>
</td>
</tr></table>
<!-- END: main -->