#!/bin/bash
cur_dir=$(cd "$(dirname "$0")"; pwd)
cd $cur_dir
php crontab.php
