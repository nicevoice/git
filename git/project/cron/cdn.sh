#/bin/bash

# CmsTop Cdn Control Script

#要监控的目录，末尾必须以/结束
HomeDir="/www/cmstop/public/"

#日志文件路径
LOGFILE="/var/log/cmstop_cdn.log"

CURDIR=$(cd "$(dirname "$0")"; pwd)

if [ ! -f $LOGFILE ]; then
	touch $LOGFILE
fi

PSCHECK="$(ps a)"
if [[ -n $(echo $PSCHECK | grep "$CURDIR/cdn/write") ]]; then
	echo "Waining: cmstop cdn control queue write is already running!"
else
	echo "cmstop cdn control queue write start"
	$CURDIR/cdn/write.sh $HomeDir $CURDIR >> $LOGFILE &
fi

if [[ -n $(echo $PSCHECK | grep "$CURDIR/cdn/read") ]]; then
    echo "Waining: cmstop cdn control queue read is already running!"
else
    echo "cmstop cdn control queue read start"
    $CURDIR/cdn/read.sh $HomeDir $CURDIR >> $LOGFILE &
fi

