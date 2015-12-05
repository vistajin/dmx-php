var qqFloatObj = {
	color : '#C30202',//百变由我 颜色自由定义 推荐 #000 #CCC #C30202 #48ff00
	openTitle : "客服中心",//关闭后右侧的浮动提示窗的题目
	closeTitle : "联<br/>系<br/>我<br/>们<br/>",//qq浮动窗口的题目
	style : "left:1px; top:50px;position: absolute;z-index:100",//这里是css定义浮动窗口的位置，顶部距离
	width : "width:168px;",//这里定义浮动窗口的宽度
	qqstyle : 7,//貌似1-9这里就是qq在线图片的显示样式
	isOpen : false, //默认展开：true,默认收缩：false
	other : "联系方式<br />15811872988<br />QQ-766320202",//这里可以编辑html
	qqlist : "售前咨询：|766320202,售后咨询：|766320202,技术支持：|766320202,建议/投诉：|766320202"
//多个qq用,隔开,QQ和客服名用|隔开
};

var _$ = [
		'display:none;',
		'display:block;',
		'display:block;',
		'display:none;',
		'',
		",",
		"|",
		'<div class="item"><span>',
		'</span><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=',
		//'</span><a target="_blank" href="tencent://message/?uin=',
		'&site=qq&menu=yes"><img src="http://wpa.qq.com/pa?p=2:',
		':',
		'&time=',
		'" title="点击这里给我发消息" /></a></div>',
		'',
		'<style type="text/css">',
		"#qqFloatWin {width:168px; height:auto; border:2px solid #C30202;}",
		".qqFloatWin_title{background:url(qqfloatwin/qq_float_win_title_bg.png) #C30202;_background: #C30202;}",
		".qqFloatWin_title{padding:8px 10px; font-size:14px; color:#FFF;font-family:'微软雅黑','黑体'; position:relative}",
		".qqFloatWin_close_btn{line-height:32px;align:right}",
		".qqFloatWin_close_btn{width:25px;height:25px; cursor:pointer;position:absolute;top: 0px;right: 10px;}",
		".qqFloatWin_list{font-size:14px; color:#444; font-size:13px; line-height:20px; padding:10px; background:#FFF}",
		".qqFloatWin_list .item{height:30px}",
		".qqFloatWin_list span{display:block; width:47%; text-align:right; overflow:hidden;white-space:nowrap; height:30px; float:left}",
		".qqFloatWin_list a{display:block; width:52%; float:left; overflow:hidden; height:30px; border:0}",
		".qqFloatWin_list a img{border:0px; display:inline-block; padding:0; margin:0}",
		".qqFloatWin_list .other {border-top:1px dotted #ccc; margin-top:10px;font-family:'微软雅黑','黑体'; font-size:17px; text-align:center; line-height:28px; padding-top:5px}",
		"#qqFloatBtn {background: url(/dmx/Public/qqonline/qq.gif) no-repeat  #C30202;  background-position:8px 15px}",
		"#qqFloatBtn {width:31px; display:none; color:#FFF; font-size:15px; text-align:center; line-height:20px; padding-top:45px; cursor:pointer}",
		'</style>',
		'<div id="qqFloatWin_con" style="@style@" >',
		'  <div id="qqFloatBtn" onclick="qqFloatWinOpen()" style="',
		'" >@closeTitle@</div>',
		'  <div id="qqFloatWin" style="@width@">',
		'      <div id="qqFloatWin_title" class="qqFloatWin_title">@openTitle@',
		'      <div onclick="qqFloatWinClose()" class="qqFloatWin_close_btn">x</div>',
		'      </div>', '<div class="qqFloatWin_list">', '        @qqlist@',
		'        <div class="other">', '        @other@', '        </div>',
		'      </div>', '  </div>', '</div>', '@other@', '@qqlist@', '@width@',
		'@style@', '@closeTitle@', '@openTitle@', 'qqFloatWin',
		'qqFloatWin_title', 'qqFloatBtn', 'qqFloatWin', 'none', 'qqFloatBtn',
		'block', 'qqFloatWin', 'block', 'qqFloatBtn', 'none', "qqFloatWin_con",
		"qqFloatWin_con", "px", "qqFloatWinSlider()" ];

qqFloatWinTime = new Date().getTime();
if (qqFloatObj.isOpen) {
	qqFloatWinBtnDisplay = _$[0];
	qqFloatWinDivDisplay = _$[1]
} else {
	qqFloatWinBtnDisplay = _$[2];
	qqFloatWinDivDisplay = _$[3]
};
qqFloatObj.width = qqFloatWinDivDisplay + qqFloatObj.width;
var a = new Array();
var b = _$[4];
a = qqFloatObj.qqlist.split(_$[5]);
for (i = 0x0; i < a.length; i++) {
	var d = new Array();
	d = a[i].split(_$[6]);
	b += _$[7] + d[0x0] + _$[8] + d[0x1] + _$[9] + d[0x1] + _$[10]
			+ qqFloatObj.qqstyle + _$[11] + qqFloatWinTime + _$[12]
};
qqFloatWinTpl = _$[13];
qqFloatWinTpl += _$[14];
qqFloatWinTpl += _$[15];
qqFloatWinTpl += _$[16];
qqFloatWinTpl += _$[17];
qqFloatWinTpl += _$[18];
qqFloatWinTpl += _$[19];
qqFloatWinTpl += _$[20];
qqFloatWinTpl += _$[21];
qqFloatWinTpl += _$[22];
qqFloatWinTpl += _$[23];
qqFloatWinTpl += _$[24];
qqFloatWinTpl += _$[25];
qqFloatWinTpl += _$[26];
qqFloatWinTpl += _$[27];
qqFloatWinTpl += _$[28];
qqFloatWinTpl += _$[29];
qqFloatWinTpl += _$[30] + qqFloatWinBtnDisplay + _$[31];
qqFloatWinTpl += _$[32];
qqFloatWinTpl += _$[33];
qqFloatWinTpl += _$[34];
qqFloatWinTpl += _$[35];
qqFloatWinTpl += _$[36];
qqFloatWinTpl += _$[37];
qqFloatWinTpl += _$[38];
qqFloatWinTpl += _$[39];
qqFloatWinTpl += _$[40];
qqFloatWinTpl += _$[41];
qqFloatWinTpl += _$[42];
qqFloatWinTpl += _$[43];
qqFloatWinTpl = qqFloatWinTpl.replace(_$[44], qqFloatObj.other);
qqFloatWinTpl = qqFloatWinTpl.replace(_$[45], b);
qqFloatWinTpl = qqFloatWinTpl.replace(_$[46], qqFloatObj.width);
qqFloatWinTpl = qqFloatWinTpl.replace(_$[47], qqFloatObj.style);
qqFloatWinTpl = qqFloatWinTpl.replace(_$[48], qqFloatObj.closeTitle);
qqFloatWinTpl = qqFloatWinTpl.replace(_$[49], qqFloatObj.openTitle);
qqFloatWinTpl = qqFloatWinTpl.replace(/#C30202/g, qqFloatObj.color);
document.write(qqFloatWinTpl);
function c(d) {
	document.getElementById(_$[50]).style.borderColor = d;
	document.getElementById(_$[51]).style.backgroundColor = d;
	document.getElementById(_$[52]).style.backgroundColor = d
};
function qqFloatWinClose() {
	document.getElementById(_$[53]).style.display = _$[54];
	document.getElementById(_$[55]).style.display = _$[56]
};
function qqFloatWinOpen() {
	document.getElementById(_$[57]).style.display = _$[58];
	document.getElementById(_$[59]).style.display = _$[60]
};
qqFloatWinlastScrollY = 0x0;
function qqFloatWinSlider() {
	var d;
	if (document.documentElement && document.documentElement.scrollTop) {
		d = document.documentElement.scrollTop
	} else if (document.body) {
		d = document.body.scrollTop
	} else {
	}
	;
	percent = .1 * (d - qqFloatWinlastScrollY);
	if (percent > 0x0) {
		percent = Math.ceil(percent)
	} else {
		percent = Math.floor(percent)
	}
	;
	document.getElementById(_$[61]).style.top = parseInt(document
			.getElementById(_$[62]).style.top)
			+ percent + _$[63];
	qqFloatWinlastScrollY = qqFloatWinlastScrollY + percent
};
window.setInterval(_$[64], 0x1);