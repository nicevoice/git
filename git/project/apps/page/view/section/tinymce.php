<?php $this->display('header', 'system');?>
<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="tiny_mce/editor.js"></script>
<table width="98%" height="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8" >
  <tr>
    <td><textarea style="width:100%;height:100%" id="content" name="content"></textarea></td>
  </tr>
  <tr>
    <td style="text-align:center"><button type="button" onclick="dialogCallback.ok()" class="button_style_2">确定</button><button type="button" onclick="dialogCallback.cancel()" class="button_style_1">取消</button></td>
  </tr>
</table>
<script type="text/javascript">
editor('content', 's_mini');
</script>
</body>
</html>