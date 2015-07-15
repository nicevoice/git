  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <p class="f_l">
        <input type="button" name="refresh" onclick="tableApp.load();" value="刷新" class="button_style_1"/>
        <input type="button" name="pass" onclick="question.pass();" value="通过" class="button_style_1"/>
        <input type="button" name="commend" onclick="question.commend();" value="推荐" class="button_style_1"/>
        <input type="button" name="remove" onclick="question.remove();" value="删除" class="button_style_1"/>
	    <input type="button" name="iplock" onclick="question.ipLock();" value="IP 锁定" class="button_style_1"/>  
    </p>
  </div>
  <!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="pass"><a href="#question.pass">通过</a></li>
   <li class="commend"><a href="#question.commend">推荐</a></li>
   <li class="edit"><a href="#question.edit">修改</a></li>
   <li class="delete"><a href="#question.remove">删除</a></li>
   <li class="iplock"><a href="#question.ipLock">IP 锁定</a></li>
</ul>

<script type="text/javascript">
var row_template = '<tr id="row_{questionid}">\
                        <td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{questionid}" value="{questionid}" /></td>\
	                	<td>{content}</td>\
	                	<td class="t_c">\
	                	   <img src="images/sh.gif"  name="推荐给主持人"  height="16" width="16" align="absmiddle" class="hand" onclick="question.commend({questionid})"/>&nbsp;&nbsp;\
	                	   <img src="images/del.gif" name="删除提问"  height="16" width="16" align="absmiddle" class="hand" onclick="question.remove({questionid})"/>&nbsp;&nbsp;\
                        </td>\
	                	<td class="t_c">{nickname}</td>\
	                	<td class="t_c">{created}</td>\
	                	<td class="t_c">{ip}({iparea})</td>\
	                </tr>';

function init_row_event(id, tr)
{
	tr.find('img').attrTips('name', '', 100);
}
</script>