var rttC, curBool=tMR=true, curLeft=curTop=curX1=curY1=curZ=rtXd=rtZd=0;
function falign() {
	$("#sTxt")[0].style.top = $("#mode")[0].value==5||($("#mode")[0].value==4&&$("#rcTxt")[0].clientHeight>($("#wideS")[0].checked?422:381)) ? ($("#rcTxt")[0].clientHeight>($("#wideS")[0].checked?422:381)?"-1px":"0px") : "";
	$("#sTxt")[0].style.bottom = $("#mode")[0].value==4&&$("#rcTxt")[0].clientHeight<=($("#wideS")[0].checked?422:381) ? ($("#wideS")[0].checked?"-4px":"-2px") : "";
	$("#alphaS")[0].disabled = $("#borderC")[0].disabled = $("#fontF")[0].disabled = $("#zRT")[0].disabled = $("#yRT")[0].disabled = $("#col1")[0].disabled = $("#row1")[0].disabled = $("#pcA")[0].disabled = $("#mode")[0].value!=7 ? true : false;
	dQZ();
	sRT();
	bcC();
}
function ffs() {
	var fSv = $("#fSize")[0].value;
	if(fSv < 2) {fSv = 2;}
	else if(fSv > 127) {fSv = 127;}
	$("#txtshow")[0].style.fontSize = fSv+"px";
	$("#txtshow")[0].style.lineHeight = (fSv==32||fSv==96) ? (navigator.userAgent.indexOf('Firefox')<0?1+3.36/fSv:1+3.04/fSv) : (navigator.userAgent.indexOf('Firefox')<0?1+2.48/fSv:1+2.008/fSv);
	if($("#mode")[0].value!=7) { falign(); }
}
function fco() {
	if ($("#fColor")[0].value.match(/^[a-f0-9]{6}$/i)) {
		$("#txtshow")[0].style.color = "#"+$("#fColor")[0].value;
		bcC();
	}
}
function fex() {
	var showEx = $("#msgBox")[0].value.replace(/^ +| +$/g,"");
	showEx = showEx.replace(/\&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
	$("#txtshow")[0].innerHTML = showEx.replace(/\r\n/g,"<br />").replace(/\t/g,"<pre style='display:inline;'>\t</pre>");
	if($("#mode")[0].value!=7) { falign(); }
}
function dQZ() {
	var rcVR = /^\-?\d+\.?\d*$/;
	var row = rcVR.test($("#row1")[0].value) ? ($("#row1")[0].value>0&&$("#row1")[0].value<1 ? Math.round($("#row1")[0].value*($("#wideS")[0].checked?422:384)) : $("#row1")[0].value)-1 : -1;
	var col = rcVR.test($("#col1")[0].value) ? ($("#col1")[0].value>0&&$("#col1")[0].value<1 ? Math.round($("#col1")[0].value*($("#wideS")[0].checked?950:541)) : $("#col1")[0].value) : 0;
	$("#sTxt")[0].style.height = $("#mode")[0].value==7 ? ($("#wideS")[0].checked?"422px":"386px") : "";
	$("#sTxt")[0].style.textAlign = $("#mode")[0].value==7 ? "left" : "";
	$("#rcTxt")[0].style.top = $("#mode")[0].value==7 ? row+"px" : "";
	$("#rcTxt")[0].style.left = $("#mode")[0].value==7 ? col+"px" : "";
	$("#rcTxt")[0].style.margin = $("#mode")[0].value==7 ? "" : "0px auto";
}
function fap() {
	var aSv = /^[01]+\.?\d*$/;
	$("#txtshow")[0].style.opacity = aSv.test($("#alphaS")[0].value)&&$("#mode")[0].value==7 ? $("#alphaS")[0].value : "1";
}
function bcC() {
	var bCv = $("#fColor")[0].value!="000000" ? "rgba(0,0,0,0.7)" : "rgba(255,255,255,0.7)";
	$("#txtshow")[0].style.textShadow = !$("#borderC")[0].checked&&$("#mode")[0].value==7 ? "none" : "0px 1px 1px "+bCv+", 1px 0px 1px "+bCv+", 0px -1px 1px "+bCv+", -1px 0px 1px "+bCv;
}
function fFont() {
	var faceF = $("#fontF")[0].value&&$("#mode")[0].value==7 ? '"'+$("#fontF")[0].value+'",' : '';
	$("#txtshow")[0].style.fontFamily = faceF+'"黑体",SimHei,"蘋果儷中黑","幼圆","宋体",Arial';
}
function sRT() {
	var rtVR = /^\-?\d+\.?\d*$/;
	rtZd = rtVR.test($("#zRT")[0].value)&&$("#mode")[0].value==7 ? $("#zRT")[0].value : 0;
	rtXd = rtVR.test($("#yRT")[0].value)&&$("#mode")[0].value==7 ? -$("#yRT")[0].value : 0;
	var pPtest = "perspectiveProperty" in document.documentElement.style||"msPerspective" in document.documentElement.style||"OPerspective" in document.documentElement.style||"MozPerspective" in document.documentElement.style||"WebkitPerspective" in document.documentElement.style;
	$("#txtshow")[0].style.transform = $("#txtshow")[0].style.msTransform = $("#txtshow")[0].style.OTransform = $("#txtshow")[0].style.MozTransform = $("#txtshow")[0].style.WebkitTransform = pPtest ? "rotateZ("+rtZd+"deg) rotateY("+rtXd+"deg)" : "rotate("+rtZd+"deg)";
}
function wsSet() {
	$("#msgshow")[0].style.width = $("#wideS")[0].checked ? "950px" : "";
	$("#msgshow")[0].style.height = $("#wideS")[0].checked ? "422px" : "";
	$("#sTxt")[0].style.width = $("#wideS")[0].checked ? "960px" : "";
	dQZ();
}
function olShow() {
	$("#txtshow")[0].style.outline = $("#outlineS")[0].checked ? "aqua solid thin" : "";
}
function hShow() {
	if ($("#hideS")[0].value == "隐藏预览") {
		$("#msgshow")[0].style.display = "none";
		$("#hideS")[0].value = "显示预览";
	} else {
		$("#msgshow")[0].style.display = "";
		$("#hideS")[0].value = "隐藏预览";
	}
}
function rcShow() {
	if($("#mode")[0].value!=7) {return false;}
	if(curBool){
		curBool = false;
		rttC = 1;
		$("#msgshow")[0].style.overflow = "visible";
		curLeft = $("#rcTxt")[0].offsetLeft;
		$("#rcTxt")[0].style.left = curLeft + "px";
		curTop = $("#rcTxt")[0].offsetTop;
		$("#rcTxt")[0].style.top = curTop + "px";
		document.addEventListener("keydown",rtSet,true);
		document.addEventListener("mousemove",rcMove,true);
	} else {
		rcStop();
	}
}
function rtSet(e) {
	if(!e) {e = window.event;}
	if(e.ctrlKey) {
		rcStop();
	} else if(e.keyCode==(68||100)) {
		$("#fSize")[0].value = Math.round($("#fSize")[0].value) + 2;
		ffs();
	} else if(e.keyCode==(83||115)) {
		$("#fSize")[0].value = Math.round($("#fSize")[0].value) - 2;
		ffs();
	} else if(e.keyCode==(66||98)) {
		$("#pcA")[0].checked = $("#pcA")[0].checked ? false : true;
	} else {
		tMR = true;
		if(e.keyCode==49) {rttC = 1;}
	//	else if(e.keyCode==50) {rttC = 2;}
		else if(e.keyCode==51) {rttC = 3;}
		else if(e.keyCode==52) {rttC = 4;}
	//	else if(e.keyCode==53) {rttC = 5;}
		else if(e.keyCode==54) {rttC = 6;}
	}
}
function rcMove(e) {
	if(!e) {e = window.event;}
	var curX2=e.clientX, curY2=e.clientY;
	if(tMR) {
		tMR = false;
		if(rttC==1) {curLeft = $("#rcTxt")[0].offsetLeft;}
		if(rttC==1) {curTop = $("#rcTxt")[0].offsetTop;}
		curX1 = e.clientX - ((rttC==3 || rttC==5 || rttC==6) ? rtXd : 0);
		curY1 = e.clientY - ((rttC==2 || rttC==5 || rttC==6) ? rtYd : 0);
		if(rttC==4) {curZ = e.clientX+e.clientY-rtZd;}
	}
	if(rttC == 1) {
		$("#rcTxt")[0].style.left = (curX2-curX1+curLeft) + "px";
		$("#rcTxt")[0].style.top = (curY2-curY1+curTop) + "px";
		$("#col1")[0].value = $("#pcA")[0].checked ? Math.round((curX2-curX1+curLeft)*1000/($("#wideS")[0].checked?950:541))/1000 : curX2-curX1+curLeft;
		$("#row1")[0].value = $("#pcA")[0].checked ? Math.round((curY2-curY1+curTop+1)*1000/($("#wideS")[0].checked?422:384))/1000 : curY2-curY1+curTop+1;
	} else {
		if(rttC==3 || rttC==5 || rttC==6) {$("#yRT")[0].value = rtXd = curX1-curX2;}
	//	if(rttC==2 || rttC==5) {$("#xRT")[0].value = rtYd = curY1-curY2;}
		if(rttC==4) {$("#zRT")[0].value = rtZd = curX2+curY2-curZ;}
		if(rttC==6) {$("#zRT")[0].value = rtZd = curY2-curY1;}
		sRT();
	}
}
function rcStop() {
	curBool = tMR = true;
	document.removeEventListener("mousemove",rcMove,true);
	document.removeEventListener("keydown",rtSet,true);
	$("#msgshow")[0].style.overflow = "";
	dQZ();
}
window.onload = function () {
	ffs();
	fco();
	fFont();
	olShow();
}