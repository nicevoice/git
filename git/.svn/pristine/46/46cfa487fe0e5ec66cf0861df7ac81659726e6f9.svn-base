
<form id="text_add" method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>" enctype="Multipart/form-data">
    <input type="hidden" name="questionid" value="<?=$questionid?>" />
    <input type="hidden" name="type"  value="<?=$type?>" />
    <table border="0" cellspacing="0" cellpadding="0" class="table_form">
        <tr>
            <th width="80"><span class="c_red">*</span> 标题：</th>
            <td><input type="text" name="subject" id="subject" class="bdr inputtit_focus"  value="<?=$subject?>" size="100"/> </td>
        </tr>
        <tr>
            <th width="70"><span class="c_red">*</span> 科目：</th>
            <td><?=property_once('subjectid','subjectid',$pro_ids['subjectid'],$subjectid)?> </td>
        </tr>
        <tr>
            <th width="70"><span class="c_red">*</span> 考点：</th>
            <td><?=property_once('knowledgeid','knowledgeid',$pro_ids['knowledgeid'],$knowledgeid)?> </td>
        </tr>
        <tr>
            <th width="70"><span class="c_red">*</span> 类型：</th>
            <td><?=property_once('qtypeid','qtypeid',$pro_ids['qtypeid'],$qtypeid)?> </td>
        </tr>
        <tr>
            <th width="70"><span class="c_red">*</span> 来源：</th>
            <td><input type="text" name="source" value="<?=$source?>"></td>
        </tr>
        <tr>
            <th>内容：</th>
            <td><textarea name="description" id="description" style="height:100px; width: 400px;"><?=$description?></textarea><script>$('#description').editor(undefined, {'onchange_callback':'editCallback'});var editCallback = function() {window.changed = true;};</script></td>
        </tr>
        <tr>
            <th>必填：</th>
            <td><input type="checkbox" id="required" name="required"  value="1" class="bdr_5" <?=$required ? 'checked' : ''?> /> 是</td>
        </tr>
        <tr>
            <th>绑定题目</th>
            <td><ul><li style="float: left; margin-right: 10px;"><a href="javascript:;" onclick="question.band('radio')">单选</a></li>
                    <li  style="float: left; margin-right: 10px;"><a href="javascript:;" onclick="question.band('checkbox')">多选</a></li>
                    <li  style="float: left; margin-right: 10px;"><a href="javascript:;" onclick="question.band('text')">单行文本</a></li>
                    <li  style="float: left; margin-right: 10px;"><a href="javascript:;" onclick="question.band('textarea')">多行文本</a></li>
                    <li  style="float: left; margin-right: 10px;"><a href="javascript:;" onclick="question.band('select')">下拉菜单</a></li></ul></td>
        </tr>
        <tr id="bind_list">
            <th>绑定列表：</th>
            <td><?php $qs = loader::model('admin/question','exam')->select(array('bandid'=>$questionid)); if ($qs):?>
                    <?php


                    foreach($qs as $k=>$q):

                        ?>
                        <dd id="bandid_<?=$q['questionid']?>"><?=++$k?>.<?=$q['subject']?><input type="hidden" value="<?=$q['questionid']?>" name="qided[]"><a style="margin-left: 10px;" href="javascript:void(0);" onclick="question.removeband(<?=$q['questionid']?>, 'del')">删除</a><hr></dd>

                    <?php endforeach;?>

                <?php endif;?></td>
        </tr>
    </table>
</form>
