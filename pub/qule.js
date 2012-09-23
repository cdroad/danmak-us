
function lrurl(){var htmlos=document.getElementById('bofqi').innerHTML;var stringoshtmls=htmlos.split('flashvars="');var dzurl=document.location.href;dzurl=dzurl.split('video/av');if(!dzurl[1]||stringoshtmls==undefined||stringoshtmls[1]==undefined)
{document.getElementById("link1").value="该视频无法引用!";document.getElementById("link2").value="该视频无法引用!";}
else
{stringoshtmls=stringoshtmls[1].split('"');dzurl=dzurl[1].split('/');document.getElementById("link1").value="http://static.loli.my/miniloader.swf?"+stringoshtmls[0];document.getElementById("link2").value='<embed height="452" width="544" quality="high" allowfullscreen="true" type="application/x-shockwave-flash" src="http://static.loli.my/miniloader.swf" flashvars="'+stringoshtmls[0]+'" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash"></embed>';}}
function copy_clip(copy){if(window.clipboardData){window.clipboardData.setData("Text",copy);}
else if(window.netscape){netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');var clip=Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);if(!clip)return;var trans=Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);if(!trans)return;trans.addDataFlavor('text/unicode');var str=new Object();var len=new Object();var str=Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);var copytext=copy;str.data=copytext;trans.setTransferData("text/unicode",str,copytext.length*2);var clipid=Components.interfaces.nsIClipboard;if(!clip)return false;clip.setData(trans,null,clipid.kGlobalClipboard);}
document.getElementById("copyinfos").innerHTML="复制成功了哦!";return false;}
var i_loginMsg=null;function getLoginMsg()
{if(typeof document.getElementById("isnewmsg")=="undefined")return;if(document.getElementById("isnewreport")!=undefined&&document.getElementById("isnewreport")!=null)
{if(document.getElementById("isnewreport").innerHTML=="TRUE"&&confirm("您的视频中有举报留言，是否立即查看?"))
window.location.href="/member/dmm.php?mode=report";}
clearInterval(i_loginMsg);if(document.getElementById("isnewmsg").innerHTML=="FALSE")return;var _oriTitle=top.document.title;var _titleFlushId=0;setInterval(function()
{if(_titleFlushId++%2==0)
{top.document.title="【新消息】"+_oriTitle;document.getElementById("l_msg").style.color="#ff0000";}else
{top.document.title="【　　　】"+_oriTitle;document.getElementById("l_msg").style.color="";}},1000);}
function _CheckLogin(){var taget_obj=document.getElementById('_userlogin');myajax=new DedeAjax(taget_obj,false,false,'','','');myajax.SendGet2("/member/ajax_loginsta.php?type=ajax");DedeXHTTP=null;i_loginMsg=setInterval(getLoginMsg,500);}
function _UpdateLogin(){var taget_obj=document.getElementById('cacheupdate');myajax=new DedeAjax(taget_obj,false,false,'','','');myajax.SendGet2("http://bilibili.tv/dad.php");DedeXHTTP=null;myajax=new DedeAjax(taget_obj,false,false,'','','');myajax.SendGet2("http://bilibili.tv/member/ajax_loginsta.php?r="+Math.random());DedeXHTTP=null;}
function _ajax_login(){var taget_obj=document.getElementById('_userlogin');myajax=new DedeAjax(taget_obj,false,false,'','','');myajax.AddKeyUtf8('fmdo','login');myajax.AddKeyUtf8('dopost','login');myajax.AddKeyUtf8('keeptime','604800');myajax.AddKeyUtf8('userid',document.getElementById('ajax_userid').value);myajax.AddKeyUtf8('pwd',document.getElementById('ajax_pwd').value);myajax.AddKeyUtf8('vdcode',document.getElementById('ajax_vdcode').value);myajax.SendPost2("/member/index_do.php");myajax.ClearSet();myajax.SendGet2("/member/ajax_loginsta.php?r="+Math.random());}
function _ajax_logout(){var taget_obj=document.getElementById('_userlogin');myajax=new DedeAjax(taget_obj,false,false,'','','');myajax.SendGet2("/member/index_do.php?fmdo=login&dopost=exit");myajax.SendGet2("/member/ajax_loginsta.php?r="+Math.random());}
function CheckLogin(){var taget_obj=document.getElementById('_ajax_feedback');myajax=new DedeAjax(taget_obj,false,false,'','','');myajax.SendGet2("/member/ajax_feedback.php");DedeXHTTP=null;}
function getDigg(aid)
{var taget_obj=document.getElementById('newdigg');myajax=new DedeAjax(taget_obj,false,false,'','','');myajax.SendGet2("/plus/feedback.php?action=showDigg&aid="+aid);DedeXHTTP=null;}
function _onvdcodeBlur()
{if(document.getElementById("_vdcodes").value=="")
{document.getElementById("_vdcodes").value="请输入验证码";}
document.getElementById("_yzm").innerHTML="";}
function _onvdcodeFocus()
{document.getElementById('_vdcodes').value="";document.getElementById("_yzm").innerHTML="<img src='/include/vdimgck.php?r="+Math.random()+"' />";}
function lgtags(){var tag_a=document.getElementsByTagName("span");for(i in tag_a){var offset=6;var num=5;if(tag_a[i].className=="tag"){tag_a[i].onclick=function(){$DE('search-keyword').value=this.innerHTML;$DE('searchform').submit();};var rnd=Math.ceil((num+offset)*Math.random());if(rnd>offset){tag_a[i].className="tag"+(rnd-offset);}}}}
function xinptitleth()
{try{var _span=document.getElementById("xinpbox").getElementsByTagName("span");for(i in _span)
{if(_span[i].innerHTML.indexOf("【")!=-1&&_span[i].innerHTML.indexOf("】")!=-1)
{_span[i].innerHTML=_span[i].innerHTML.replace(_span[i].innerHTML.substring(_span[i].innerHTML.indexOf("【"),_span[i].innerHTML.indexOf("】")+1),"");}}}
catch(err){}}
function swlm(){var _lm=document.getElementById("inlanmu").getElementsByTagName("a");for(i in _lm)
{if($DE('search-keyword').value.indexOf("@"+_lm[i].innerHTML)!=-1)
{_lm[i].style.backgroundColor="#F36";}
_lm[i].onclick=function(){var p=null;if((p=$DE('search-keyword').value.indexOf("@"+this.innerHTML))==-1)
{$DE('search-keyword').value=$DE('search-keyword').value+"@"+this.innerHTML;$DE('searchform').submit();}else
{$DE('search-keyword').value=$DE('search-keyword').value.substr(0,p)+$DE('search-keyword').value.substr(p+this.innerHTML.length+1);$DE('searchform').submit();}}}}
function addNewTag()
{len=0;$DE("newtag").innerHTML="<form action=\"/plus/tagadd.php\" target=\"_blank\" method=\"post\"><input type=\"hidden\" name=\"aid\" value=\""+aid+"\" /><input type=\"text\" value=\"\" name=\"newtag_in\" size=\"20\" /> <input type=\"submit\" value=\"新增\" /></form>";return false;}
function scrollTo(x,y)
{return window.scroll(x,y);}
var nodedata=[];var kwlist=[];function ltitles(){tobj=document.getElementById('dedepagetitles');k=0;if(tobj==undefined)return;for(i=0;i<tobj.length;i++){nodedata[k++]=[tobj.options[i].innerHTML,tobj.options[i].value];};al=document.getElementById("alist");al.innerHTML="";if(pageno=='')pageno=1;pageno=parseInt(pageno);sid=(k>3?pageno-2:0);for(i=sid;i<pageno+2;i++)
{if(nodedata[i]==undefined)continue;if(i==pageno-1)
{al.innerHTML+="<span class=\"curPage\">"+nodedata[i][0]+"</span>\n";}else
{al.innerHTML+="<a href=\""+nodedata[i][1]+"\">"+nodedata[i][0]+"</a>\n";}}
if(k>3)
{al.innerHTML+="<a href=\"#\" onclick=\"viewallalist()\">...</a>";}}
function viewallalist()
{al=document.getElementById("alist");al.innerHTML="";alHTM="";for(i=0;i<nodedata.length;i++)
{if(i==pageno-1)
{alHTM+="<span class=\"curPage\">"+nodedata[i][0]+"</span>\n";}else
{alHTM+="<a href=\""+nodedata[i][1]+"\">"+nodedata[i][0]+"</a>\n";}}
al.innerHTML=alHTM;}
function callSpecPart(cpage)
{if(cpage&&totalpage&&nodedata!=undefined&&cpage>0)
{if(cpage<totalpage&&cpage<=nodedata.length&&(top.allowSwitchPart==undefined||top.allowSwitchPart))
{window.location=nodedata[cpage-1][1];}else if(window.parent.callNextSpec!=undefined)
{window.parent.callNextSpec();}}}
function callNextPart()
{callSpecPart(pageno+1);}
function onfirefox()
{var Sys={};var ua=navigator.userAgent.toLowerCase();var s;(s=ua.match(/msie ([\d.]+)/))?Sys.ie=s[1]:(s=ua.match(/firefox\/([\d.]+)/))?Sys.firefox=s[1]:(s=ua.match(/chrome\/([\d.]+)/))?Sys.chrome=s[1]:(s=ua.match(/opera.([\d.]+)/))?Sys.opera=s[1]:(s=ua.match(/version\/([\d.]+).*safari/))?Sys.safari=s[1]:0;if(Sys.safari)return 0;if(Sys.ie)return 0;if(Sys.firefox)return 1;if(Sys.chrome)return 2;if(Sys.opera){return 2;}
return 0;}
function loadhttpsplayer(qid)
{document.getElementById("bofqi").innerHTML='<embed height="482" width="950" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" flashvars="vid='+qid+'" src="https://secure.bilibili.tv/httpsplayer.swf" type="application/x-shockwave-flash" allowfullscreen="true" quality="high"></embed>';}
function loadQQplayer(qid)
{document.getElementById("bofqi").innerHTML='<iframe height="482" style="margin-top:10px;" width="950" src="https://secure.bilibili.tv/qq,'+qid+'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';}
function GetCookie(cookieName)
{var theCookie=""+document.cookie;var ind=theCookie.indexOf(cookieName);if(ind==-1||cookieName=="")
return"";var ind1=theCookie.indexOf(';',ind);if(ind1==-1)
ind1=theCookie.length;return unescape(theCookie.substring(ind+cookieName.length+1,ind1));}
function flv_checkLogin()
{if(GetCookie('DedeUserID')!==undefined&&GetCookie('DedeUserID'))
{return true;}else
{return false;}}
function checkBrowser()
{if($.browser.mozilla)return;if($("#browser_tips").length)return;tips_str="<div class=\"ui-widget\" id=\"browser_tips\" style=\"margin:0px;\">"+" <div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em;\"> "+"   <ul id=\"icons\" class=\"ui-widget ui-helper-clearfix\" style=\"width:19px;float:right\">"+"    <li class=\"ui-state-default ui-corner-all\" style=\"cursor:pointer;\" onclick=\"$('#browser_tips').toggle( 'fold', {}, 500 );\"><span class=\"ui-icon ui-icon-closethick\"></span></li> "+"   </ul>"+"  <p id=\"browser_tips_msg\"><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;margin-top:.1em;\"></span></p>"+" </div>"+"</div>";var browser="未知";if($.browser.webkit)
{if(navigator.userAgent.match(/(chrome)[ \/]([\w.]+)/i))
{browser="Chrome";}else
{browser="WebKit";}}else if($.browser.safari)
{browser="Safari";}else if($.browser.opera)
{browser="Opera";}else if($.browser.msie)
{browser="IE";}
fc=$($(".main")[0].firstChild);fc.before(tips_str);$("#browser_tips_msg")[0].innerHTML+="哔哩小贴士：您正在使用的是<strong>"+browser+"</strong>浏览器，如果无法观看本视频，请尝试使用<a href=\"http://www.mozillaonline.com/\" target=\"_blank\"><storng>Firefox</strong></b>！";}
function kwtags(_kwlist)
{kwlist=_kwlist;var chNode=$('embed');for(var i=0;i<chNode.length;i++){if(chNode[i]&&$(chNode[i]).attr('flashvars').indexOf("file=")!=-1)
{checkBrowser();break;}}
if(typeof(kwlist)=="undefined")return;sh="";for(i=0;i<kwlist.length;i++)
{if(kwlist[i].match(/^([sn]m|av)[0-9]+$/))
{sh+="<a href=\"http://acg.tv/"+kwlist[i]+"\" target=\"_blank\" class=\"tag\">"+kwlist[i]+"</a> ";}else
{sh+="<a href=\"#\" class=\"tag\" onclick=\"$DE('search-keyword').value=this.innerHTML;$DE('searchform').submit()\">"+kwlist[i]+"</a> ";}}
document.getElementById("tag_doc").innerHTML=sh;if(keywords_change)
{$DE("newtag").innerHTML='<a href="#" onclick="return addNewTag()">增加新TAG</a>';}
if($('iframe[src^="https://secure.bilibili.tv"]').size()>0)
{setInterval(function(){if(evalCode=__GetCookie('__secureJS'))
{__SetCookie('__secureJS','');eval(evalCode);}},1000);}}
var autofresh_interval=null;function init_autofresh(aid,mid)
{var refresh_func=function()
{var scrObj=document.createElement("script");scrObj.src="/plus/count.php?papa=yes&reload="+Math.random()+"&aid="+aid+"&mid="+mid;scrObj.type="text/javascript";scrObj.language="javascript";document.body.appendChild(scrObj);};autofresh_interval=setInterval(refresh_func,30000);}
function heimu(api,b)
{var heimu=document.getElementById("heimu");if(b==0)
{document.getElementById("heimu").style.display="none";}
else
{document.getElementById("heimu").style.opacity="."+api/10;document.getElementById("heimu").style.filter="alpha(opacity="+api+")";document.getElementById("heimu").style.display="block";document.getElementById("heimu").style.position="fixed";}}
function XHConn()
{var xmlhttp=false,bComplete=false;try{xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}
catch(e){try{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
catch(e){try{xmlhttp=new XMLHttpRequest();}
catch(e){xmlhttp=false;}}}
if(!xmlhttp)return null;this.connect=function(sURL,sMethod,sVars,fnDone)
{if(!xmlhttp)return false;bComplete=false;sMethod=sMethod.toUpperCase();try{if(sMethod=="GET")
{xmlhttp.open(sMethod,sURL+"?"+sVars,true);sVars="";}
else
{xmlhttp.open(sMethod,sURL,true);xmlhttp.setRequestHeader("Method","POST "+sURL+" HTTP/1.1");xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");}
xmlhttp.onreadystatechange=function(){if(xmlhttp.readyState==4&&!bComplete)
{bComplete=true;fnDone(xmlhttp);}};xmlhttp.send(sVars);}
catch(z){return false;}
return true;};return this;}
function sendLog(url,tp)
{var myConn=new XHConn();if(myConn){var fnNull=function(sXML){}
var request="url="+encodeURIComponent(url)+"&tp="+encodeURIComponent(tp)+"&refer="+document.referrer+"&uid="+GetCookie('DedeUserID');myConn.connect("https://secure.bilibili.tv/track","GET",request,fnNull);}}
var trackTime;var adCheckInt;var isAdTrackSent=false;var isCProTrackSent=false;var isAlTrackSent=false;function trackCProLog(){sendLog(document.location,"b");isCProTrackSent=true;}
function trackAdLog(){sendLog(document.location,"g");isAdTrackSent=true;}
function trackAlistLog(){sendLog(document.location,"al");isAlTrackSent=true;}
function trackAdTimeOver(){trackTime=new Date();clearInterval(adCheckInt);if(!isAdTrackSent)adCheckInt=setInterval(function(){clearInterval(adCheckInt);trackAdLog();},3000);}
function trackCProTimeOver(){trackTime=new Date();clearInterval(adCheckInt);if(!isCProTrackSent)adCheckInt=setInterval(function(){clearInterval(adCheckInt);trackCProLog();},3000);}
function trackAlTimeOver(){trackTime=new Date();clearInterval(adCheckInt);if(!isAlTrackSent)adCheckInt=setInterval(function(){clearInterval(adCheckInt);trackAlistLog();},3000);}
var _ads_bindCount=0;function bindElement(){var e=document.getElementsByTagName("iframe");for(var i=0;i<e.length;i++){if((e[i].id.indexOf('aswift')>-1||e[i].src.indexOf('googleads.g.doubleclick.net')>-1)&&!GetCookie('adTrack')&&GetCookie('DedeUserID')){e[i].onmousedown=trackAdLog;e[i].onmouseover=trackAdTimeOver;e[i].onactivate=e[i].onfocusin=e[i].onfocus=trackAdLog;e[i].contentWindow.onactivate=e[i].contentWindow.onfocusin=e[i].contentWindow.onfocus=trackAdLog;_ads_bindCount++;}else if(e[i].src.indexOf('cpro.baidu.com')>-1&&!GetCookie('cpTrack')&&GetCookie('DedeUserID')){e[i].onmousedown=trackCProLog;e[i].onmouseover=trackCProTimeOver;e[i].onactivate=trackCProLog;e[i].onfocusin=e[i].onfocus=trackCProLog;e[i].contentWindow.onactivate=e[i].contentWindow.onfocusin=e[i].contentWindow.onfocus=trackCProLog;_ads_bindCount++;}}
if(typeof(console.log)!="undefined")console.log("bind: "+_ads_bindCount);var e=document.getElementsByTagName("div");for(var i=0;i<e.length;i++){if(e[i].id=='comm_content'||e[i].className=="bottom"||e[i].className=="bottom"||e[i].className=="tagcontainer"||e[i].className=="scontent")
{e[i].onmouseover=function(){clearInterval(adCheckInt);};}}}
function __updateLoginSta(dat)
{var targetObj=document.getElementById('_userlogin');targetObj.innerHTML=dat;}
function __js_debug(msg)
{if(typeof(console.log)!="undefined")
{console.log('PADplayer:'+msg);}}
function js_viewallalist()
{al=document.getElementById("alist");al.innerHTML="";for(i=0;i<pagelist.length;i++)
{if(i==pageno-1)
{al.innerHTML+="<span class=\"curPage\">"+pagelist[i].str+"</span>\n";}else
{al.innerHTML+="<a href=\""+pagelist[i].url+"\">"+pagelist[i].str+"</a>\n";}}}
function loadco(){clearInterval(int_fblogin);if(typeof(AjaxPage)!="undefined")AjaxPage(1);};function loadArt(phpurl)
{document.write('<script src="'+phpurl+'/count.php?papa=yes&aid='+aid+'&mid='+art_mid+'" language="javascript">'+unescape('%3C/script%3E'));document.write('<script src="'+phpurl+'/tags.php?aid='+aid+'" language="javascript">'+unescape('%3C/script%3E'));var int_fblogin=flv_checkLogin()?null:setInterval(function(){if(flv_checkLogin())loadco();},1000);$("#titles").html(art_title);$("#art_position").html(art_position);$("#art_pubdate").html(art_pubdate);$(".intro").html(art_desc);if(art_desc!="")$(".intro").css("display","");$("#stowvideo")[0].href=phpurl+'/stow.php?aid='+aid;$("#bofqi").html(art_body);if(memberinfo!=null)
{$("#u-member-face").attr("src",memberinfo.face);$(".usname").html("<a href=\"/member/index.php?uid="+memberinfo.userid+"\" target=\"_blank\">"+memberinfo.uname+"</a>");$(".msggo").html("<a href=\"/member/pm.php?dopost=send&uid="+memberinfo.userid+"\" target=\"_blank\">发送短信</a>");$(".spacemore").html("<a href=\"/member/index.php?uid="+memberinfo.userid+"\" target=\"_blank\">space..</a>");$(".sign").html(memberinfo.sign);$(".a-uinfo").css("display","");}
lrurl();al=document.getElementById("alist");al.innerHTML="";k=pagelist.length;if(pageno=='')pageno=1;pageno=parseInt(pageno);sid=(k>3?pageno-2:0);for(i=sid;i<pageno+2;i++)
{if(pagelist[i]==undefined)continue;if(i==pageno-1)
{al.innerHTML+="<span class=\"curPage\">"+pagelist[i].str+"</span>\n";}else
{al.innerHTML+="<a href=\""+pagelist[i].url+"\">"+pagelist[i].str+"</a>\n";}}
if(k>3)
{al.innerHTML+="<a href=\"#\" onclick=\"js_viewallalist()\">...</a>";}
function loadco(){clearInterval(int_fblogin);loadfeeback(aid,1);};function AjaxPage(p){loadfeeback(aid,p)}
function AjaxPage_tg(p){loadfeeback_tg(aid,p)}}
function player_ff_resize(){$("embed[src=\"http://static.loli.my/play.swf\"]").css("height",$(window).height());}
function player_ff_scroll(){$("embed[src=\"http://static.loli.my/play.swf\"]").css("margin-top",$(window).scrollTop());}
function player_ff_fullwin(status)
{if(status)
{$("#hd_bg").hide();$("#num").hide();$(".viewbox").hide();$(".a-uinfo").hide();$("#minibottom").hide();$("#bofqi").css("height","100%");$("#bofqi").css("margin","0px");$("#bofqi").css("padding","0px");$(".logobg").css("width","100%");$(".main").css("width","100%");$(".z").css("width","100%");$("#bofqi").css("width","100%");$("embed[src=\"http://static.loli.my/play.swf\"]").css("width","100%");$("embed[src=\"http://static.loli.my/play.swf\"]").css("padding-top","0px");$(window).resize(player_ff_resize);$(window).scroll(player_ff_scroll);player_ff_resize();player_ff_scroll();}else{$("#hd_bg").show();$("#num").show();$(".viewbox").show();$(".a-uinfo").show();$("#bofqi").css("height","");$("#bofqi").css("margin","");$("#bofqi").css("padding","");$(".logobg").css("width","");$(".main").css("width","");$(".z").css("width","");$("#bofqi").css("width","");$("embed[src=\"http://static.loli.my/play.swf\"]").css("width","950px");$("embed[src=\"http://static.loli.my/play.swf\"]").css("height","482px");$("embed[src=\"http://static.loli.my/play.swf\"]").css("padding-top","");$("embed[src=\"http://static.loli.my/play.swf\"]").css("margin-top","");$(window).unbind('resize',player_ff_resize);$(window).unbind('scroll',player_ff_scroll);}}
var player_bottom_visible=null;function player_fullwin(status)
{if(status)
{$("#contgg1").hide();player_bottom_visible=false;if($("#minibottom").css("display")!="none")
{player_bottom_visible=true;$("#minibottom").hide();}}else
{$("#contgg1").show();if(player_bottom_visible)$("#minibottom").show();}
if($.browser.mozilla)return player_ff_fullwin(status);if(status)
{$("#bofqi").removeClass("scontent");$("#bofqi").addClass("scontent_fullscreen");$("embed[src=\"http://static.loli.my/play.swf\"]").css("width","100%");$("embed[src=\"http://static.loli.my/play.swf\"]").css("height","100%");$(".main").css("position","static");}else{$("#bofqi").removeClass("scontent_fullscreen");$("#bofqi").addClass("scontent");$("embed[src=\"http://static.loli.my/play.swf\"]").css("width","950");$("embed[src=\"http://static.loli.my/play.swf\"]").css("height","470");$(".main").css("position","relative");}}
function player_widewin()
{$("embed[src=\"http://static.loli.my/play.swf\"]").css("width","950");$("embed[src=\"http://static.loli.my/play.swf\"]").css("height","588");}
function showStow(aid,obj)
{if(!$("#dialog-modal").length)
{$('<div id="dialog-modal" title="收藏视频" style="display:none;padding:0px"></div>').appendTo("body");}
$.ajax({url:"/plus/stow.php?aid="+aid+"&ajax=1",type:"GET",dataType:"html",async:true,success:function(msg){$("#dialog-modal").html(msg);$("#dialog:ui-dialog").dialog("destroy");$("#dialog-modal").dialog({width:530,height:150,position:['center',obj.offsetTop+170],resizable:false});}});return false;}
function shareTo163(info,url,img)
{var url='link=http://www.bilibili.tv/&source='+encodeURIComponent('哔哩哔哩弹幕视频网')
+'&info='+encodeURIComponent(info)+' '+encodeURIComponent(url)
+'&images='+img+'&togImg=true';window.open('http://t.163.com/article/user/checkLogin.do?'+url+'&'+new Date().getTime(),'newwindow','height=330,width=550,top='+(screen.height-280)/2+',left='+(screen.width-550)/2+', toolbar=no, menubar=no, scrollbars=no,resizable=yes,location=no, status=no');return false;}
function shareToSina(info,url,img)
{var url='http://v.t.sina.com.cn/share/share.php?title='+encodeURIComponent(info)+'&url='+url+'&source=嗶哩嗶哩&sourceUrl=http%3A%2F%2Fwww.bilibili.tv%2F&pic='+img+'&appkey=4114772851';window.open(url,'','toolbar=0,resizable=1,scrollbars=yes,status=1,width=600,height=450');return false;}
function shareToQQ(info,url,img){var _u='http://v.t.qq.com/share/share.php?title='+encodeURIComponent(info)+'&url='+url+'&appkey=84435a83a11c484881aba8548c6e7340&site=http://www.bilibili.tv/&pic='+img;window.open(_u,'','width=700, height=680, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, location=yes, resizable=no, status=no');return false;}
function shareToSohu(info,url,img)
{var url='http://t.sohu.com/third/post.jsp?url='+url+'&title='+encodeURIComponent(info)+'&content=utf-8&pic='+img;window.open(url,'','toolbar=0,resizable=1,scrollbars=yes,status=1,width=600,height=450');return false;}