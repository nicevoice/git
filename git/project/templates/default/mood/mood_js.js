var mood_img_url = IMG_URL+'apps/mood/';
var votehtml= '<p><span class="f_r"><a href="/mood/" target="_blank">查看心情排行</a></span>'
+'<span class="f_l b">您看到此篇文章时的感受是：</span></p><ul class="clear layout">'
{loop $infos $info}
+'<li><img onclick="javascript:vote({$info[moodid]});return false;" alt={$info[name]}" src="'+mood_img_url+'{$info[image]}"/>'
+'<br/>{$info[name]}<br/><input type="radio" onclick="javascript:vote({$info[moodid]});return false;" name="moodradio" value="1"/></li>'
{/loop}
+'</ul>';
$("#mood").html(votehtml);

function vote(vote_id) {
	if ( ( new Date().getTime() - $.cookie('mood_time'+contentid) ) > 1000*({$votetime}+1) ) {
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
.mood_bar_in {background:url({IMG_URL}apps/mood/images/moodrank.gif) repeat-y;bottom:0;left:0;position:absolute;width:34px;}\
</style>\
<div class="titles layout">\
<h3 style="text-align:left;"><span class="f_r" style="width: 80px;"><a target="_blank" href="/mood/">查看心情排行</a></span>\
已经有 <font color="red" id="vote_total"></font> 人表态：</h3>\
	<ul id="clear layout">\
	{loop $infos $info}
	<li id="m{$info[moodid]}_li">\
	<div class="mood_bar"><div class="mood_bar_in" id="m{$info[moodid]}_bar"></div></div>\
	{$info[name]}<br/><font></font>人\
	</li>\
{/loop}
</ul></div>';