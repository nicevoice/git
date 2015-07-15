  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <p class="f_l">
        <input type="button" name="refresh" onclick="tableApp.load();" value="刷新" class="button_style_1"/>
        <input type="button" name="delete" onclick="question.del();" value="彻底删除" class="button_style_1"/>
        <input type="button" name="clear" onclick="question.clear();" value="全部清空" class="button_style_1"/>
	    <input type="button" name="iplock" onclick="question.ipLock();" value="IP 锁定" class="button_style_1"/> 
	    <input type="button" name="ipunlock" onclick="question.ipUnlock();" value="IP 解锁" class="button_style_1"/> 
    </p>
  </div>
  <!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#question.edit">修改</a></li>
   <li class="delete"><a href="#question.del">彻底删除</a></li>
   <li class="clear"><a href="#question.clear">全部清空</a></li>
   <li class="iplock"><a href="#question.ipLock">IP 锁定</a></li>
   <li class="ipunlock"><a href="#question.ipUnlock">IP 解锁</a></li>
</ul>

<script type="text/javascript">
var row_template = '<tr id="row_{questionid}">\
                        <td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{questionid}" value="{questionid}" /></td>\
	                	<td><span name="iplocked">{iplocked}</span> {content}</td>\
	                	<td class="t_c">\
	                	   <img src="images/del.gif" name="彻底删除"  height="16" width="16" align="absmiddle" class="hand" onclick="question.del({questionid})"/>&nbsp;&nbsp;\
                        </td>\
	                	<td class="t_c">{nickname}</td>\
	                	<td class="t_c">{created}</td>\
	                	<td class="t_c">{ip}({iparea})</td>\
	                </tr>';

function init_row_event(id, tr)
{
	var lock = tr.find('span[name="iplocked"]');
	var html = lock.html() > '0' ? '<img src="images/lock.gif" name="IP 被锁定" />' : '';
	lock.html(html);
	tr.find('img').attrTips('name', '', 100);
}
</script>