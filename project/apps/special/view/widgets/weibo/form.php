<style>
.table_args {}
.table_args tr{line-height:24px;}
.table_args tr td{}
</style>
<div class="bk_8"></div>
<form>
	<table width="95%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<th width="80">选择类型：</th>
				<td>
					<label><input type="radio" name="data[type]" value="live" <?php if($data['type'] == 'live' || empty($data['type'])) echo 'checked'; ?>/> 微博直播</label>  <label><input type="radio" name="data[type]" value="show" <?php if($data['type'] == 'show') echo 'checked'; ?>/> 微博秀</label>
				</td>
			</tr>
			<tr>
				<th>微博服务商：</th>
				<td>
					<select name="data[provider]">
						<option value="sina" <?php if($data['provider'] == 'sina') echo 'selected'; ?>>新浪</option>
						<option value="qq" <?php if($data['provider'] == 'qq') echo 'selected'; ?>>腾讯</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>代码生成器：</th>
				<td><p id="weibo_generator" style="word-wrap:break-word;word-break: normal;">&nbsp;</p></td>
			</tr>
			<tr id="weibo_args" style="display:none;">
				<th>参数配置：</th>
				<td>
					<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_args">
						<tbody rel="sina" style="display:none;">
							<tr>
								<th width="60">关键词：</th>
								<td><input type="text" name="sina[tags]" value="<?php echo $data['sina']['tags']?$data['sina']['tags']:'cmstop';?>"/></td>
							</tr>
							<tr>
								<th>样式：</th>
								<td><input type="text" name="sina[skin]" value="<?php echo $data['sina']['skin']?$data['sina']['skin']:1;?>" size="4"/> 值为1到4</td>
							</tr>
							<tr>
								<th>显示图片：</th>
								<td><input type="text" name="sina[isShowPic]" value="<?php echo $data['sina']['isShowPic']?$data['sina']['isShowPic']:1;?>" size="4"/> 1为显示，2为不显示</td>
							</tr>
							<tr>
								<th>宽度：</th>
								<td><input type="text" name="sina[width]" value="<?php echo $data['sina']['width']?$data['sina']['width']:'100%';?>" size="4"/> 例如：300或100% 下同</td>
							</tr>
							<tr>
								<th>高度：</th>
								<td><input type="text" name="sina[height]" value="<?php echo $data['sina']['height']?$data['sina']['height']:500;?>" size="4"/></td>
							</tr>
							<tr>
								<th>条数：</th>
								<td><input type="text" name="sina[mblogNum]" value="<?php echo $data['sina']['mblogNum']?$data['sina']['mblogNum']:10;?>" size="4"/></td>
							</tr>
						</tbody>
						<tbody rel="qq" style="display:none;">
                            <tr>
                                <td colspan="2">请点击上面的链接生成代码，然后返回粘贴到下面</td>
                            </tr>
						</tbody>
					</table>
				</td>
			</tr>
            <tr id="show_args" style="display:none;">
				<th>参数配置：</th>
				<td>
                    <table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_args">
						<tbody rel="sina" style="display:none;">
							<tr>
                                <td colspan="2">请点击上面的链接生成代码，然后返回粘贴到下面</td>
                            </tr>
						</tbody>
                        <tbody rel="qq" style="display:none;">
							<tr>
								<td colspan="2">请点击上面的链接生成代码，然后返回粘贴到下面</td>
							</tr>
						</tbody>
					</table>
                </td>
            </tr>
			<tr>
				<th>调用代码：</th>
				<td>
					<textarea name="data[code]" cols="45" rows="10"><?php echo $data['code'];?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</form>