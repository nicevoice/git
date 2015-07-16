<div class="bk_8"></div>
<!--form name="system_research_search" id="system_research_search" method="GET" action="?app=system&controller=research&action=search&search=do"-->
   <input type="hidden" name="catid" value="<?=$catid?>" />
   <input type="hidden" name="status" value="<?=$status?>" />
   <input type="hidden" name="modelid" value="<?=$modelid?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th>关键词：</th>
    <td><input type="text" name="keywords" value="<?=$keywords?>" size="15" maxlength="20"/></td>
  </tr>
  <!--tr>
    <th>发布人：</th>
    <td><input type="text" name="creator" value="<?=$creator?>" size="15"/></td>
  </tr>
   <tr>
    <th>时间：</th>
    <td><input type="text" name="published_start" value="<?=$published_start?>" size="12"/> ~ <input type="text" name="published_end" value="<?=$published_end?>" size="12"/></td>
  </tr>
  <tr>
    <th>权重：</th>
    <td><input type="text" name="weight" value="<?=$weight_start?>" size="3"/> ~ <input type="text" name="weight" value="<?=$weight_end?>" size="3"/></td>
  </tr>
  <tr>
    <th>来源：</th>
    <td><input type="text" name="source" value="<?=$source?>" size="15"/></td>
  </tr-->
</table>
<!--/form-->