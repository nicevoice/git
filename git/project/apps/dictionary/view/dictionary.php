<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
@CHARSET "UTF-8";

html{background:#f9f9f9;}body{background:#fff;color:#333;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;margin:2em auto;width:960px;padding:1em 2em;-moz-border-radius:11px;-khtml-border-radius:11px;-webkit-border-radius:11px;border-radius:11px;border:1px solid #dfdfdf;}a{color:#2583ad;text-decoration:none;}a:hover{color:#d54e21;}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px Georgia,"Times New Roman",Times,serif;margin:5px 0 0 -4px;padding:0;padding-bottom:7px;}h2{font-size:16px;}p,li,dd,dt{padding-bottom:2px;font-size:12px;line-height:18px;}code,.code{font-size:13px;}ul,ol,dl{padding:5px 5px 5px 22px;}a img{border:0;}abbr{border:0;font-variant:normal;}#logo{margin:6px 0 14px 0;border-bottom:none;text-align:center;}.step{margin:20px 0 15px;}.step,th{text-align:left;padding:0;}.submit input,.button,.button-secondary{font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;text-decoration:none;font-size:14px!important;line-height:16px;padding:6px 12px;cursor:pointer;border:1px solid #bbb;color:#464646;-moz-border-radius:15px;-khtml-border-radius:15px;-webkit-border-radius:15px;border-radius:15px;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;-khtml-box-sizing:content-box;box-sizing:content-box;}.button:hover,.button-secondary:hover,.submit input:hover{color:#000;border-color:#666;}.button,.submit input,.button-secondary{background:#f2f2f2;}.button:active,.submit input:active,.button-secondary:active{background:#eee;}textarea{border:1px solid #bbb;-moz-border-radius:4px;-khtml-border-radius:4px;-webkit-border-radius:4px;border-radius:4px;}.form-table{border-collapse:collapse;margin-top:1em;width:100%;}.form-table td{margin-bottom:9px;padding:10px;border-bottom:8px solid #fff;font-size:12px;}.form-table th{font-size:13px;text-align:left;padding:16px 10px 10px 10px;border-bottom:8px solid #fff;width:130px;vertical-align:top;}.form-table tr{background:#f3f3f3;}.form-table code{line-height:18px;font-size:18px;}.form-table p{margin:4px 0 0 0;font-size:11px;}.form-table input{line-height:20px;font-size:15px;padding:2px;}.form-table th p{font-weight:normal;}#error-page{margin-top:50px;}#error-page p{font-size:12px;line-height:18px;margin:25px 0 20px;}#error-page code,.code{font-family:Consolas,Monaco,Courier,monospace;}#pass-strength-result{background-color:#eee;border-color:#ddd!important;border-style:solid;border-width:1px;margin:5px 5px 5px 1px;padding:5px;text-align:center;width:200px;}#pass-strength-result.bad{background-color:#ffb78c;border-color:#ff853c!important;}#pass-strength-result.good{background-color:#ffec8b;border-color:#fc0!important;}#pass-strength-result.short{background-color:#ffa0a0;border-color:#f04040!important;}#pass-strength-result.strong{background-color:#c3ff88;border-color:#8dff1c!important;}.message{border:1px solid #e6db55;padding:.3em .6em;margin:5px 0 15px;background-color:#ffffe0;}
.toptext {}
.normal {}
.fieldheader {}
tr:hover {background:#EAF2FA;}
td {padding:5px; border:solid 1px #CCC;}
.fieldcolumn {font-weight:bold; text-align:left;}
.comment {width:420px;}
.header {}
.headtext {color:#093E56; text-shadow:0 1px 0 #FFFFFF; -moz-border-radius:6px 6px 0 0; font-size:14px; font-weight:bold; line-height:1; margin:0; padding:7px 9px; -moz-user-select:none; background:#D5E6F2; cursor:pointer;}
td.headtext {border-bottom:0;}
.fields {font-size:14px; padding:10px; border:1px solid #ECECEC; border-collapse:collapse;}
br.page {page-break-after: always}
a {display:block; margin-top:10px; text-align:right;}
li a {display:inline;}
ul li {width:45%; float:left; margin:5px 30px 5px 0;}
</style>
<TITLE>CmsTop 数据字典</TITLE>
</head>
<body bgcolor='#ffffff' topmargin="0">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
<tr>
<h1><a href='?app=dictionary&controller=dictionary&action=index&download=dictionary' style="display:inline;float:right;margin:0">下载</a><span>CmsTop 数据字典<?php echo date("Y-m-d");?></span></h1>
</tr>
</table >
<a name="header">&nbsp</a><ul>
<?php foreach ($table_info as  $key => $table){ ?>
	<li><a href="#<?= $key ?>"><?= $key ?>&nbsp;&nbsp;<?= $table ?></a></li>
<?php } ?>
</ul>
<br class=page>
<p style="clear:both; margin-top:50px;">&nbsp;</p>
<?php foreach ($fields as $key=>$list){ ?>
		
		<p><a name='<?= $key ?>'>&nbsp</a>
			<table width="100%" border="0" cellspacing="0" cellpadding="3">
			<tr>
				<td class="headtext" width="30%" align="left" valign="top"><?= $key ?>&nbsp;&nbsp;<?=$table_info[$key] ?></td>
			</tr>
			</table>
			<table class="fields" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center" valign="top" class="fieldcolumn">字段名</td>
					<td align="center" valign="top" class="fieldcolumn">字段类型</td>
					<td align="center" valign="top" class="fieldcolumn">Null</td>
					<td align="center" valign="top" class="fieldcolumn">主键</td>
					<td align="center" valign="top" class="fieldcolumn">默认值</td>
					<td align="center" valign="top" class="fieldcolumn">自增</td>
					<td align="center" valign="top" class="fieldcolumn comment">注释</td>
				</tr>
				<?php foreach ($list as $value){ ?>
				<tr>
					<?php foreach ($value as $v){ ?>
						<td align="left" valign="top"><?= $v ?></td>
					<?php } ?>
				</tr>
				<?php } ?>	
			</table>
			<a href="#header">返回顶部</a>
	<?php } ?>	
</body>