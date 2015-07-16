#!/bin/bash

CDNHOMEDIR=$1
CURDIR=$2

echo "CmsTop CdnControl Queue read start $(date +"%F %T")"

while [ 1 == 1 ]
do
	/usr/bin/php $CURDIR/cdn/notify.php  --do=exec
	sleep 1
done
