<?php $this->display('header', 'system');?>
<div class="bk_8"></div>
<textarea id="note"><?=$note?></textarea>
<div class="note-info">
	<span id="lastUpdate">
		最后更新时间：
		<span><?=date('Y-m-d H:i:s', $lastmodified)?></span>
	</span>
	<input type="button" value="保存" class="btn large primary" onclick="saveNote();" />
	<input type="button" value="取消" class="btn large" onclick="clearNote();" />
</div>
<div class="bk_8"></div>
<script type="text/javascript">
var note = "<?=$note?>";
var textarea = $('textarea');
var saveNote = function() {
	$.post('?app=system&controller=my&action=note', {note: textarea.val()}, function (data){
		if(data.state) {
			note = textarea.val();
			$('#lastUpdate span').text(data.time);
		} else {
			ct.error('更新出错');
		}
	}, 'json');
}
var clearNote = function() {
	textarea.val(note);
}
</script>
<style>
#note {
	width: 500px;
	height: 300px;
	display: block;
	margin-bottom: 30px;
	margin-left: 10px;
}
#lastUpdate {
	font-weight: bold;
	font-size: 14px;
	margin-left: 10px;
	margin-right: 60px;
}
#lastUpdate span {
	color: #a00;
}

.btn {
  cursor: pointer;
  display: inline-block;
  background-color: #e6e6e6;
  background-repeat: no-repeat;
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), color-stop(25%, #ffffff), to(#e6e6e6));
  background-image: -webkit-linear-gradient(#ffffff, #ffffff 25%, #e6e6e6);
  background-image: -moz-linear-gradient(top, #ffffff, #ffffff 25%, #e6e6e6);
  background-image: -ms-linear-gradient(#ffffff, #ffffff 25%, #e6e6e6);
  background-image: -o-linear-gradient(#ffffff, #ffffff 25%, #e6e6e6);
  background-image: linear-gradient(#ffffff, #ffffff 25%, #e6e6e6);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#e6e6e6', GradientType=0);
  padding: 5px 14px 6px;
  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
  color: #333;
  font-size: 13px;
  line-height: normal;
  border: 1px solid #ccc;
  border-bottom-color: #bbb;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  -webkit-transition: 0.1s linear all;
  -moz-transition: 0.1s linear all;
  -ms-transition: 0.1s linear all;
  -o-transition: 0.1s linear all;
  transition: 0.1s linear all;
  margin-left: 20px;
  vertical-align: middle;
}
.btn:hover {
  background-position: 0 -15px;
  color: #333;
  text-decoration: none;
}
.btn:focus {
  outline: 1px dotted #666;
}
.btn.primary {
  color: #ffffff;
  background-color: #0064cd;
  background-repeat: repeat-x;
  background-image: -khtml-gradient(linear, left top, left bottom, from(#049cdb), to(#0064cd));
  background-image: -moz-linear-gradient(top, #049cdb, #0064cd);
  background-image: -ms-linear-gradient(top, #049cdb, #0064cd);
  background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #049cdb), color-stop(100%, #0064cd));
  background-image: -webkit-linear-gradient(top, #049cdb, #0064cd);
  background-image: -o-linear-gradient(top, #049cdb, #0064cd);
  background-image: linear-gradient(top, #049cdb, #0064cd);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#049cdb', endColorstr='#0064cd', GradientType=0);
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
  border-color: #0064cd #0064cd #003f81;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
}
.btn.active, .btn:active {
  -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.25), 0 1px 2px rgba(0, 0, 0, 0.05);
  -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.25), 0 1px 2px rgba(0, 0, 0, 0.05);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.25), 0 1px 2px rgba(0, 0, 0, 0.05);
}
button.btn::-moz-focus-inner, input[type=submit].btn::-moz-focus-inner {
  padding: 0;
  border: 0;
}

.note-info { margin-left:10px}
</style>