<?='<?xml version="1.0" encoding="'.$CONFIG['charset'].'"?>'?>
<!-- bocolor为背景色（十六进制），bgimg为背景图片地址（没用就用none） -->
<<?=$report_name?> bgcolor="0xFFFFFF" bgimg="none">
<? foreach ($data as $data) { ?>
	<pie>
		<!-- pie名：字符串 -->
		<pie_mcname><?=$report_name?></pie_mcname>
		<!-- pie深度：整数 -->
		<pie_depth>2</pie_depth>
		<!-- 标题：字符串。格式：标题(字符串),x坐标(数字),y坐标(数字),文字大小(数字),是否粗体(true是/false否),颜色（十六进制） -->
		<pie_title><?=$data['subject']?>,210,40,16,true,0x000000</pie_title>
		<!-- 竖向列标支持动作:onRelease,onRollOver,onRollOut,none，任选或除none之外的全部/部份用“,”隔开 -->
		<pie_action>onRelease</pie_action>
		<!-- 数值：数字。每一个项目数据用“,”隔开 -->
		<pie_num><?=$data['votes']?></pie_num>
		<!-- 名称：字符串。每一个项目名称用“,”隔开 -->
		<pie_name><?=$data['options']?></pie_name>
		<!-- 颜色：字符串（十六进制颜色名）。每一个项目颜色用“,”隔开 -->
		<pie_color><?=$data['colors']?></pie_color>
		<!-- 是否显示名称与数值：true显示/false不显示。每一个项目(饼上)是否显示用“,”隔开 -->
		<pie_showtitle><?=$data['showtitle']?></pie_showtitle>
		<!-- 默认pie位置是否打开：true打开/false不打开。默认当前一个项目是否为打开状态用“,”隔开 -->
		<pie_defaultsate><?=$data['defaultsate']?></pie_defaultsate>
		<!-- 深色系数：立体色深色深度（在大小1的一个数字默认为1.43） -->
		<pie_dark>1.43</pie_dark>
		<!-- 圆心x坐标：数字 -->
		<pie_x0>300</pie_x0>
		<!-- 圆心y坐标：数字 -->
		<pie_y0>200</pie_y0>
		<!-- 长轴：数字 -->
		<pie_z>75</pie_z>
		<!-- 短轴：数字 -->
		<pie_d>45</pie_d>
		<!-- 环宽：数字 -->
		<pie_w>75</pie_w>
		<!-- 高：数字 -->
		<pie_h>15</pie_h>
		<!-- 移动距离：打开一个饼图的一块之后移动的距离（数字） -->
		<pie_move>5</pie_move>
		<!-- 点击之后的pie透明度：数字（0-100） -->
		<pie_clickalpha>100</pie_clickalpha>
		<!-- 默认pie的透明度：数字（0-100） -->
		<pie_defaultalpha>50</pie_defaultalpha>
		<!-- 是否显示竖向列标：true（显示）或false（不显示） -->
		<pie_showorder>true</pie_showorder>
		<!-- 竖向列标x坐标：数字 -->
		<pie_orderx>500</pie_orderx>
		<!-- 竖向列标y坐标：数字 -->
		<pie_ordery>60</pie_ordery>
		<!-- 竖向列标间隔：数字 -->
		<pie_orderxy>6</pie_orderxy>
		<!-- 竖向列标文字颜色：颜色（十六进制） -->
		<pie_orderfontcolor>0x000000</pie_orderfontcolor>
		<!-- 指示线颜色：颜色（十六进制） -->
		<pie_linecolor>0xCCCCCC</pie_linecolor>
		<!-- 指示线透明度：（0-100） -->
		<pie_linealpha>100</pie_linealpha>
		<!-- 指示线长度：数字 -->
		<pie_linewidth>25</pie_linewidth>
		<!-- 文字颜色：颜色（十六进制） -->
		<pie_fontcolor>0xFF0000</pie_fontcolor>
		<!--是否显示百分数值等（all都显示,num只显示数字,font只显示文字,none不显示）-->
		<pie_percentshow>all</pie_percentshow>
	</pie>
<? } ?>
</<?=$report_name?>>