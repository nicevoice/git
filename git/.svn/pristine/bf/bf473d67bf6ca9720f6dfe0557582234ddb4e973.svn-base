<style type="text/css">
.lh_24 {line-height:24px;}
.table_form tr td {padding:2px 0;}
</style>
<div class="tabs">
	<ul target="tbody.fortabs">
		<li>创建</li>
		<li>选择</li>
	</ul>
</div>
<form>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
	<input type="hidden" name="select_type" value="0"/>
	<input type="hidden" name="contentid" value="<?php echo $data['contentid'];?>"/>
	<input type="hidden" name="vote[modelid]" value="8">
	<input type="hidden" name="vote[status]" value="6">
	<input type="hidden" name="vote[mininterval]" value="24" />
	<tbody class="fortabs">
		<tr><td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form">
				<tr>
					<th width="60"><span class="c_red">*</span> 栏目：</th>
					<td><?=element::category('vote', 'vote[catid]',$vote['catid'])?></td>
				</tr>
				<tr>
					<th width="60"><span class="c_red">*</span> 标题：</th>
					<td><?=element::title('vote[title]', $vote['title'], $vote['color'])?></td>
					<!--
					<td><input name="vote[title]" id="data_title" size="50" maxlength="80" class="bdr inputtit_focus" type="text" />
					 <img id="title_cp" src="images/color.gif" alt="色板" style="cursor: pointer;" height="16" width="16" align="middle"/>
					 <input name="vote[color]" id="data_color" style="" type="hidden" />
					 <script src="<?=IMG_URL?>js/lib/jquery.colorPicker.js" type="text/javascript"></script>
					 <script type="text/javascript">$('#title_cp').titleColorPicker("#data_title","#data_color")</script></td>
					-->
				</tr>
				<tr>
					<th>类型：</th>
					<td class="lh_24"><input name="vote[type]" type="radio" value="radio" <? if($type != "checkbox"){?> checked="checked"<? } ?> class="checkbox_style" onclick="$('#maxoptions_span').hide();" /> 单选
						<input name="vote[type]" type="radio" class="checkbox_style" value="checkbox" <? if($type == "checkbox"){?> checked="checked"<? } ?> onclick="$('#maxoptions_span').show();" /> 多选
						<span id="maxoptions_span" <?php if($vote['type'] == "radio" || empty($vote['type'])){?>style="display:none;"<?php } ?>>最多可选  <input id="maxoptions" name="vote[maxoptions]" value="<?php echo  $vote['maxoptions'];?>" type="text" size="2"/> 项 </span>
					</td>
				</tr>
			</table>
		</td></tr>
		<tr><td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" >
				<tr>
					<th width="60" style="color:#077AC7;font-weight:normal;" class="t_r"><span class="c_red">*</span> 选项：</th>
					<td>
						<table id="vote_options" width="483" border="0" cellspacing="0" cellpadding="0" class="table_info table_list">
							<thead>
								<tr>
									<th class="bdr" width="30">排序</th>
									<th width="360">选项</th>
									<th width="60">初始票数</th>
									<th width="30">操作</th>
								</tr>
							</thead>
							<tbody id="options"></tbody>
						</table>
					</td>
				</tr>
				<tr>
					<th width="60"></th>
					<td>
                        <div class="mar_l_8 mar_5" style="margin-top:10px; margin-bottom:10px;">
						    <button class="button_style_1" onclick="option.add()" name="add_option_btn" type="button">增加选项</button>
					    </div>
                    </td>
				</tr>
			</table>
		</td></tr>
	</tbody>
	<tbody class="fortabs" style="display:none;">
		<tr><td>
		<div class="where">
				<input type="text" name="keywords" />
						<input type="hidden" name="modelid" value="8" />
						<input name="catid" width="150"
					url="?app=system&controller=category&action=cate&dsnid=&catid=%s"
					paramVal="catid"
					paramTxt="name"
					multiple="1"
					alt="选择栏目"
				/>
		</div>
		<div id="list" style="padding:3px;"></div>
		</td></tr>
	</tbody>
</table>
</form>
<!--vote-->
<script type="text/javascript" src="<?=ADMIN_URL?>apps/vote/js/option.js"></script>
<!--vote-->
<script type="text/javascript">
<?php foreach ($vote['option'] as $k=>$r):?>
option.add('<?=$r['name'];?>', '<?=$r['votes'];?>', '<?=$r['sort'];?>', '<?=$r['optionid'];?>');
<?php endforeach;?>
</script>
