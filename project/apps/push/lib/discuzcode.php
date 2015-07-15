<?php
$discuzcodes = array(
	'pcodecount' => -1,
	'codecount' => 0,
	'codehtml' => ''
);
function codedisp($code)
{
	global $discuzcodes;
	$discuzcodes['pcodecount']++;
	$code = htmlspecialchars(str_replace('\\"', '"', preg_replace('/^[\n\r]*(.+?)[\n\r]*$/is', '\1', $code)));
	$code = str_replace("\n", "<li>", $code);
	$discuzcodes['codehtml'][$discuzcodes['pcodecount']] = tpl_codedisp($code);
	$discuzcodes['codecount']++;
	return "[\tDISCUZ_CODE_$discuzcodes[pcodecount]\t]";
}
function tpl_codedisp($code)
{
	global $discuzcodes;
	return '<div class="blockcode"><div id="code'.$discuzcodes['codecount'].'"><ol><li>'.$code.'</ol></div></div>';
}
function discuzcode($message, $bbcodeoff, $htmlon)
{
	global $discuzcodes;
	if (!$bbcodeoff)
	{
		$message = preg_replace("/\s*\[code\](.+?)\[\/code\]\s*/ies", "codedisp('\\1')", $message);
	}

	if (!$htmlon)
	{
		$message = htmlspecialchars($message);
	}

	if (!$bbcodeoff)
	{
		$message = preg_replace(
			'/\[url(=((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k|thunder|synacast){1}:\/\/|www\.)([^\["\']+?))?\](.+?)\[\/url\]/ies',
			'parseurl(\'\1\', \'\5\')', $message);
		$message = preg_replace(
			'/\[email(=([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+))?\](.+?)\[\/email\]/ies',
			'parseemail(\'\1\', \'\4\')', $message);
		$message = str_replace(array(
			'[/color]', '[/size]', '[/font]', '[/align]', '[b]', '[/b]',
			'[i=s]', '[i]', '[/i]', '[u]', '[/u]', '[list]', '[list=1]', '[list=a]',
			'[list=A]', '[*]', '[/list]', '[indent]', '[/indent]', '[/float]'
		), array(
			'</font>', '</font>', '</font>', '</p>', '<strong>', '</strong>', '<i class="pstatus">', '<i>',
			'</i>', '<u>', '</u>', '<ul>', '<ul type="1" class="litype_1">', '<ul type="a" class="litype_2">',
			'<ul type="A" class="litype_3">', '<li>', '</ul>', '<blockquote>', '</blockquote>', '</span>'
		), preg_replace(array(
			'/\[color=([#\w]+?)\]/i',
			'/\[size=(\d+?)\]/i',
			'/\[size=(\d+(\.\d+)?(px|pt|in|cm|mm|pc|em|ex|%)+?)\]/i',
			'/\[font=([^\[\<]+?)\]/i',
			'/\[align=(left|center|right)\]/i',
			'/\[float=(left|right)\]/i'
		), array(
			'<font color="\1">',
			'<font size="\1">',
			'<font style="font-size: \1">',
			'<font face="\1 ">',
			'<p align="\1">',
			'<span style="float: \1;">'
		), $message));
		$nest = 0;
		while (strpos($msglower, '[table') !== FALSE
				&& strpos($msglower, '[/table]') !== FALSE)
		{
			$message = preg_replace(
				'/\[table(?:=(\d{1,4}%?)(?:,([\(\)%,#\w ]+))?)?\]\s*(.+?)\s*\[\/table\]/ies',
				'parsetable(\'\1\', \'\2\', \'\3\')', $message);
			if (++$nest > 4) break;
		}

		$message = preg_replace(array(
			'#\s*\[(quote|free)\][\n\r]*(.+?)[\n\r]*\[/\1\]\s*#is',
			'/\[media=([\w,]+)\]\s*([^\[\<\r\n]+?)\s*\[\/media\]/ies',
			'/\[swf\]\s*([^\[\<\r\n]+?)\s*\[\/swf\]/ies',
			'/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies',
			'/\[img=(\d{1,4})[x|\,](\d{1,4})\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies'
		), array(
			'<div class="quote"><blockquote>\2</blockquote></div>',
			'bbcodeurl(\'\2\', \'<a href="%s" target="_blank">%s</a>\')',
			'bbcodeurl(\'\1\', \'<a href="%s" target="_blank">Flash: %s</a> \')',
			'bbcodeurl(\'\1\', \'<img src="%s" alt="" />\')',
			'bbcodeurl(\'\3\', \'<img width="\1" height="\2" src="%s" border="0" alt="" />\')'
		), $message);
		
		for ($i = 0; $i <= $discuzcodes['pcodecount']; $i++)
		{
			$message = str_replace("[\tDISCUZ_CODE_$i\t]", $discuzcodes['codehtml'][$i], $message);
		}
		$message = preg_replace(
			'/<highlight>(.*)<\/highlight>/siU',
			'<strong><font color="#FF0000">\1</font></strong>', $message);
	}

	return $htmlon ? $message : nl2br(str_replace(array("\t", '   ', '  '), array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'), $message));
}

function parsetable($width, $bgcolor, $message) {
	if ( !preg_match("/^\[tr(?:=([\(\)%,#\w]+))?\]\s*\[td(?:=(\d{1,2}),(\d{1,2})(?:,(\d{1,4}%?))?)?\]/", $message)
		 && !preg_match("/^<tr[^>]*?>\s*<td[^>]*?>/", $message))
	{
		return str_replace('\\"', '"', preg_replace("/\[tr(?:=([\(\)%,#\w]+))?\]|\[td(?:=(\d{1,2}),(\d{1,2})(?:,(\d{1,4}%?))?)?\]|\[\/td\]|\[\/tr\]/", '', $message));
	}
	if(substr($width, -1) == '%') {
		$width = substr($width, 0, -1) <= 98 ? intval($width).'%' : '98%';
	} else {
		$width = intval($width);
		$width = $width ? ($width <= 560 ? $width.'px' : '98%') : '';
	}
	return '<table cellspacing="0" class="t_table" '.
		($width == '' ? NULL : 'style="width:'.$width.'"').
		($bgcolor ? ' bgcolor="'.$bgcolor.'">' : '>').
		str_replace('\\"', '"', preg_replace(array(
				"/\[tr(?:=([\(\)%,#\w]+))?\]\s*\[td(?:=(\d{1,2}),(\d{1,2})(?:,(\d{1,4}%?))?)?\]/ie",
				"/\[\/td\]\s*\[td(?:=(\d{1,2}),(\d{1,2})(?:,(\d{1,4}%?))?)?\]/ie",
				"/\[\/td\]\s*\[\/tr\]/i"
			), array(
				"parsetrtd('\\1', '\\2', '\\3', '\\4')",
				"parsetrtd('td', '\\1', '\\2', '\\3')",
				'</td></tr>'
			), $message)
		).'</table>';
}
function parseurl($url, $text)
{
	if (!$url && preg_match("/((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k|thunder|synacast){1}:\/\/|www\.)[^\[\"']+/i", trim($text), $matches)) {
		$url = $matches[0];
		$length = 65;
		if(strlen($url) > $length) {
			$text = substr($url, 0, intval($length * 0.5)).' ... '.substr($url, - intval($length * 0.3));
		}
		return '<a href="'.(substr(strtolower($url), 0, 4) == 'www.' ? 'http://'.$url : $url).'" target="_blank">'.$text.'</a>';
	} else {
		$url = substr($url, 1);
		if(substr(strtolower($url), 0, 4) == 'www.') {
			$url = 'http://'.$url;
		}
		return '<a href="'.$url.'" target="_blank">'.$text.'</a>';
	}
}
function parseemail($email, $text)
{
	if(!$email && preg_match("/\s*([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+)\s*/i", $text, $matches)) {
		$email = trim($matches[0]);
		return '<a href="mailto:'.$email.'">'.$email.'</a>';
	} else {
		return '<a href="mailto:'.substr($email, 1).'">'.$text.'</a>';
	}
}

function parsetrtd($bgcolor, $colspan, $rowspan, $width)
{
	return ($bgcolor == 'td' ? '</td>' : '<tr'.($bgcolor ? ' bgcolor="'.$bgcolor.'"' : '').'>').'<td'.($colspan > 1 ? ' colspan="'.$colspan.'"' : '').($rowspan > 1 ? ' rowspan="'.$rowspan.'"' : '').($width ? ' width="'.$width.'"' : '').'>';
}

function bbcodeurl($url, $tags)
{
	if(!preg_match("/<.+?>/s", $url)) {
		if(!in_array(strtolower(substr($url, 0, 6)), array('http:/', 'https:', 'ftp://', 'rtsp:/', 'mms://'))) {
			$url = 'http://'.$url;
		}
		return str_replace(array('submit', 'logging.php'), array('', ''), sprintf($tags, $url, addslashes($url)));
	} else {
		return '&nbsp;'.$url;
	}
}
