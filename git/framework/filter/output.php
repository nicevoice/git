<?php

class output
{
	static function textarea($string)
	{
		return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string)));
	}
	
	static function javascript($string)
	{
		return addslashes(str_replace(array("\r", "\n"), array('', ''), $string));
	}
}