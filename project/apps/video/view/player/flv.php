<p id="ctvideo"></p>
<script type="text/javascript" src="<?=IMG_URL?>apps/video/js/flvplayer.js"></script>
<script type="text/javascript">
var s1 = new SWFObject("<?=IMG_URL?>apps/video/js/flvplayer.swf","single","490","430","7");
s1.addParam("allowfullscreen","true");
s1.addVariable("file","<?=$video?>");
s1.addVariable("image","preview.gif");
s1.addVariable("autostart","<?=$autostart?>");
s1.addVariable("width","500");
s1.addVariable("height","430");
s1.write("ctvideo");
</script>