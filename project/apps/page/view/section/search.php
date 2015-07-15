<div class="pop_box_area">
	<div class="operation_area layout">
		<div class="search_icon" style="width:450px">
			<form>
				<input type="text" name="keywords" size="15" title="请输入关键词" value="<?=$keywords?>">
				<?=element::page('pageid', 'pageid', $pageid)?>
				<input type="submit" value="搜索" class="button_style_1" style="width:60px;*margin-top:-10px;">
			</form>
		</div>
	</div>
	<div class="attachment_lib">
		<div class="box_10 f_r" style="width:220px;">
			<h3>已选(<span id="checked_count">0</span>)</h3>
			<div class="h_350" style="height:250px;">
				<ul id="section_selected" class="txt_list">
				</ul>
			</div>
		</div>
		<div class="box_10 f_l" style="width:220px;">
			<h3 class="layout"><span class="f_l">待选(<span id="count">0 / 0</span>)</span></h3>
			<div id="scroll_div" class="h_350" style="height:250px;">
			</div>
		</div>
		<div class="f_l" style="padding:135px 0 0 2px;">
			<img src="images/move_left.gif" alt="" title="" height="16" width="30" />
		</div>
		<div class="clear"></div>
	</div>
</div>
