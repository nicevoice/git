<?php $this->display('header', 'system');?>
<style type="text/css">
.expand-action {
    text-align: left;
}
.expand-action th {
    padding-right: 10px;
    cursor: move;
}
.expand-action a, .expand-action a:hover {
    text-decoration: none !important;
    display: block;
    float: left;
    width: 16px;
    height: 16px;
    margin-right: 10px;
}
.expand-action a:hover {
}
</style>
<div class="bk_8"></div>
<form id="imgeditor_setting" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=setting">
<div style="min-height: 100px; margin-bottom: 20px;">
    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
        <caption>预设尺寸</caption>
        <tbody>
            <?php $preset_sizes = value($setting['imgeditor'], 'preset_sizes', array('width' => array(''), 'height' => array(''))); ?>
            <?php if ($preset_sizes) foreach ($preset_sizes['width'] as $index => $width): ?>
            <tr class="expand-action">
                <th width="30" class="t_l expand-count" title="拖动以排序"><?=$index + 1?></th>
                <td width="135">
                    <input type="text" name="setting[imgeditor][preset_sizes][width][]" value="<?=$width?>" placeholder="宽" size="5" />&nbsp;x&nbsp;
                    <input type="text" name="setting[imgeditor][preset_sizes][height][]" value="<?=$preset_sizes['height'][$index]?>" placeholder="高" size="5" />
                </td>
                <td>
                    <a hideFocus="true" href="javascript:void(0);" title="删除"><img class="expand-action-delete" src="images/del.gif" /></a>
                    <a hideFocus="true" href="javascript:void(0);" title="添加一行"><img class="expand-action-add" src="images/add_1.gif" /></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div style="min-height: 100px; margin-bottom: 20px;">
    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
        <caption>预设比率</caption>
        <tbody>
            <?php $preset_ratio = value($setting['imgeditor'], 'preset_ratio', array('width' => array(''), 'height' => array(''))); ?>
            <?php if ($preset_ratio) foreach ($preset_ratio['width'] as $index => $width): ?>
            <tr class="expand-action">
                <th width="30" class="t_l expand-count" title="拖动以排序"><?=$index + 1?></th>
                <td width="135">
                    <input type="text" name="setting[imgeditor][preset_ratio][width][]" value="<?=$width?>" size="5" />&nbsp;<b>:</b>&nbsp;
                    <input type="text" name="setting[imgeditor][preset_ratio][height][]" value="<?=$preset_ratio['height'][$index]?>" size="5" />
                </td>
                <td>
                    <a hideFocus="true" href="javascript:void(0);" title="删除"><img class="expand-action-delete" src="images/del.gif" /></a>
                    <a hideFocus="true" href="javascript:void(0);" title="添加一行"><img class="expand-action-add" src="images/add_1.gif" /></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
    <tr>
		<th width="40"></th>
		<td colspan="2" valign="middle"><input type="submit" id="submit" value=" 保存 " class="button_style_2"/> <span class="c_gray">拖动序号可排序，排序后记得保存</span></td>
	</tr>
</table>
</form>
<script type="text/javascript">
$(function(){
    var form = $('#imgeditor_setting');

    form.find('table').each(function() {
        var expand = $(this),
        dragInited = false,
        lastRow = expand.find('.expand-action:last'),
        append = lastRow.clone(),
        initRow = function(row) {
            row.find('.expand-action-add').click(function() {
                initRow(append.clone()).appendTo(expand);
                initCount();
                return false;
            });
            row.find('.expand-action-delete').click(function() {
                if (row.siblings().size()) {
                    row.remove();
                    initCount();
                } else {
                    row.find(':input').val('');
                }
                return false;
            });
            if (dragInited) {
                expand.sortable('refresh');
            } else {
                expand.sortable({
                    'axis': 'y',
                    'handle': 'th.expand-count',
                    'items': 'tr.expand-action',
                    'helper': 'clone',
                    'opacity': 0.6,
                    create: function() {
                        dragInited = true;
                    },
                    stop: function(ev, ui) {
                        initCount();
                    }
                });
            }
            return row;
        },
        initCount = function() {
            expand.find('.expand-count').each(function(index, item) {
                $(this).text(index + 1);
            });
        };
        expand.find('.expand-action').each(function() {
            initRow($(this));
        });
        initCount();
        if (append.find(':input').val() != '') {
            append.find(':input').val('');
            lastRow.find('.expand-action-add').trigger('click');
        }
    });

    form.ajaxForm(function(response) {
        ct.tips('保存成功');
    });
    $('.tips').attrTips('tips', 'tips_green', 200, 'top');
});
</script>
<?php $this->display('footer', 'system');?>