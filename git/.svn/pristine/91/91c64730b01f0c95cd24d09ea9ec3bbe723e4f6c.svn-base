<?php

/*$c='<div id="art_content">
</div>
<div>
<div id="art_xg">

<div id="con_da2">
<script type="text/javascript" src="http://img.jb51.net/imgby/baidu580.js"></script><script type="text/javascript" src="http://cpro.baidu.com/cpro/ui/c.js"></script><script type="text/javascript" charset="utf-8" src="http://pos.baidu.com/ecom?di=u776243&amp;tm=BAIDU_CPRO_SETJSONADSLOT&amp;fn=BAIDU_CPRO_SETJSONADSLOT&amp;baidu_id="></script><div style="display:none">-</div> <iframe id="cproIframe3" src="http://cpro.baidu.com/cpro/ui/uijs.php?tu=u776243&amp;tn=text_default_336_280&amp;n=jb51_cpr&amp;rsi1=280&amp;rsi0=336&amp;rad=&amp;rss0=%23FFFFFF&amp;rss1=%23F7FCFF&amp;rss2=%23006699&amp;rss3=%23444444&amp;rss4=%23006699&amp;rss5=&amp;rss6=%23e10900&amp;rsi5=4&amp;ts=1&amp;at=6&amp;ch=0&amp;cad=1&amp;aurl=&amp;rss7=&amp;cpa=1&amp;fv=11&amp;cn=0&amp;if=16&amp;word=http%3A%2F%2Fwww.jb51.net%2Farticle%2F31122.htm&amp;refer=http%3A%2F%2Fwww.jb51.net%2Flist%2Flist_172_1.htm&amp;ready=1&amp;jk=d1f5f49c83b8dd4e&amp;jn=3&amp;lmt=1345750001&amp;csp=1366,768&amp;csn=1366,728&amp;ccd=32&amp;chi=1&amp;cja=true&amp;cpl=37&amp;cmi=42&amp;cce=true&amp;csl=zh-CN&amp;did=3&amp;rt=8&amp;dt=1346602668&amp;pn=4:3|text_default_960_90:text_default_300_250|103:103&amp;ev=50331648&amp;c01=0&amp;prt=1346602667272" width="336" height="280" align="center,center" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" allowtransparency="true"></iframe>
</div>
</div>
<div id="numstyle"></div>
<div id="con_bo"></div>
</div>';

//$c = preg_replace("/(<div id=\"art_xg\">[^<]+<\/div>)/isU", "", $c);
$c = preg_match("/<div\sid=\"art_xg\">.*<\/div>/isU",  $c, $matches);
 
var_dump($matches);*/

define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('IN_ADMIN', 1);
define('INTERNAL', 1);

require '../../../cmstop.php';

$name ='火影忍者';

import('helper.pinyin');
$initial = pinyin::get($name,'utf-8',0);
echo $initial;