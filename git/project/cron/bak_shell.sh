#!/bin/bash
BAK_DIR="/backup"
mkdir -p ${BAK_DIR}
MYSQL_DIR="/var/lib/mysql"
CMSTOP_DIR='/www/cmstop'


#remove old bak
find $BAK_DIR -type f -mtime +7 | xargs rm -f

# bak mysql data
tar czf "${BAK_DIR}/mysql.bak.`date +%Y-%m-%d`.tar.gz" $MYSQL_DIR



# bak cmstop
tar czf "${BAK_DIR}/cmstop-`date +%Y-%m-%d`.tar.gz" ${CMSTOP_DIR}
