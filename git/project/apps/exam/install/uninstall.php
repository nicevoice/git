<?php
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
@unlink($installlog);