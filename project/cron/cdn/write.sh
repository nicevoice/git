#!/bin/bash

#配置监控的事件，具体事件种类可参考 man inotifywait
InotifyEvent="modify,move,delete"
CDNHOMEDIR=$1
CURDIR=$2

echo "CmsTop CdnControl Queue write start $(date +"%F %T")"

/usr/bin/inotifywait --timefmt '%F %T' --format '%T %w %f %e' -mrq -e $InotifyEvent $CDNHOMEDIR | while read Date Time Path FileName Event
do
	/usr/bin/php $CURDIR/cdn/notify.php --do=write --path=${Path}${FileName} --event=${Event} --time="${Date} ${Time}"
done
