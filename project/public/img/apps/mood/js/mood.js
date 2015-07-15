var mood_img_url = IMG_URL+'apps/mood/';
var votehtml= '<p><span class="f_r"><a href="/mood/" target="_blank">查看心情排行</a></span>'
+'<span class="f_l b">您看到此篇文章时的感受是：</span></p><ul class="clear layout">'
+'<li><img onclick="javascript:vote(1);return false;" alt=支持" src="'+mood_img_url+'images/zhichi.gif"/>'
+'<br/>支持<br/><input type="radio" onclick="javascript:vote(1);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(2);return false;" alt=高兴" src="'+mood_img_url+'images/gaoxing.gif"/>'
+'<br/>高兴<br/><input type="radio" onclick="javascript:vote(2);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(3);return false;" alt=震惊" src="'+mood_img_url+'images/zhenjing.gif"/>'
+'<br/>震惊<br/><input type="radio" onclick="javascript:vote(3);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(4);return false;" alt=愤怒" src="'+mood_img_url+'images/fennu.gif"/>'
+'<br/>愤怒<br/><input type="radio" onclick="javascript:vote(4);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(5);return false;" alt=无聊" src="'+mood_img_url+'images/wuliao.gif"/>'
+'<br/>无聊<br/><input type="radio" onclick="javascript:vote(5);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(6);return false;" alt=无奈" src="'+mood_img_url+'images/wunai.gif"/>'
+'<br/>无奈<br/><input type="radio" onclick="javascript:vote(6);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(7);return false;" alt=谎言" src="'+mood_img_url+'images/huangyan.gif"/>'
+'<br/>谎言<br/><input type="radio" onclick="javascript:vote(7);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(8);return false;" alt=枪稿" src="'+mood_img_url+'images/qianggao.gif"/>'
+'<br/>枪稿<br/><input type="radio" onclick="javascript:vote(8);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(9);return false;" alt=不解" src="'+mood_img_url+'images/bujie.gif"/>'
+'<br/>不解<br/><input type="radio" onclick="javascript:vote(9);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(10);return false;" alt=标题党" src="'+mood_img_url+'images/biaotidang.gif"/>'
+'<br/>标题党<br/><input type="radio" onclick="javascript:vote(10);return false;" name="moodradio" value="1"/></li>'
+'<li><img onclick="javascript:vote(11);return false;" alt=搞笑" src="'+mood_img_url+'images/gaoxiao.gif"/>'
+'<br/>搞笑<br/><input type="radio" onclick="javascript:vote(11);return false;" name="moodradio" value="1"/></li>'
+'</ul>';
$("#mood").html(votehtml);

function vote(vote_id) {
if ( ( new Date().getTime() - $.cookie('mood_time'+contentid) ) > 1000*(0+1) ) {
$.getJSON(APP_URL+"?app=mood&controller=index&action=index&contentid="+contentid+"&voteid="+vote_id+"&jsoncallback=?", function(json){
voteShow(json);
});
} else {
alert("请勿重复刷新");
$.getJSON(APP_URL+"?app=mood&controller=index&action=index&contentid="+contentid+"&jsoncallback=?", function(json){
voteShow(json);
});
}
$.cookie("mood_time"+contentid, new Date().getTime());
}
function voteShow(json) {
$("#mood").html(votedhtml).hide().fadeIn(450 | "slow");
$('#vote_total').html(json.total);
for(var i in json.data) {
$('#'+i+'_li > font').html(json.data[i].number);
$('#'+i+'_bar').css({"height": json.data[i].height+'%'}); 
}
}
var votedhtml = '<style>\
.mood_bar {position:relative; width:34px; height:100px;background:#EEF7F7;}\
.mood_bar_in {background:url(http://img.cmstop.loc/apps/mood/images/moodrank.gif) repeat-y;bottom:0;left:0;position:absolute;width:34px;}\
</style>\
<div class="titles layout">\
<h3 style="text-align:left;"><span class="f_r" style="width: 80px;"><a target="_blank" href="/mood/">查看心情排行</a></span>\
已经有 <font color="red" id="vote_total"></font> 人表态：</h3>\
<ul id="clear layout">\
<li id="m1_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m1_bar"></div></div>\
支持<br/><font></font>人\
</li>\
<li id="m2_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m2_bar"></div></div>\
高兴<br/><font></font>人\
</li>\
<li id="m3_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m3_bar"></div></div>\
震惊<br/><font></font>人\
</li>\
<li id="m4_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m4_bar"></div></div>\
愤怒<br/><font></font>人\
</li>\
<li id="m5_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m5_bar"></div></div>\
无聊<br/><font></font>人\
</li>\
<li id="m6_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m6_bar"></div></div>\
无奈<br/><font></font>人\
</li>\
<li id="m7_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m7_bar"></div></div>\
谎言<br/><font></font>人\
</li>\
<li id="m8_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m8_bar"></div></div>\
枪稿<br/><font></font>人\
</li>\
<li id="m9_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m9_bar"></div></div>\
不解<br/><font></font>人\
</li>\
<li id="m10_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m10_bar"></div></div>\
标题党<br/><font></font>人\
</li>\
<li id="m11_li">\
<div class="mood_bar"><div class="mood_bar_in" id="m11_bar"></div></div>\
搞笑<br/><font></font>人\
</li>\
</ul></div>';