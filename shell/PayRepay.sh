#!/bin/sh

#创建日志文件夹及定义日志文件名称
dir_today="/var/log/PayRepay/`date +%Y/%m`"
log_path="/var/log/PayRepay/`date +%Y/%m/%d`.log"
/bin/mkdir -p $dir_today > /dev/null 2>&1

count=`/bin/ps -ef | grep '/usr/bin/php' | grep artisan | grep PayRepay | grep -v grep | wc -l`

# zc=`/bin/ps -ef | grep '/usr/bin/php' | grep artisan | grep PayRepay | grep -v grep | wc -l`
# 不杀进程 
# if [[ $zc -gt 0 ]]; then
#    /bin/ps -ef | grep '/usr/bin/php' | grep artisan | grep PayRepay | grep -v grep | awk '{print $2}' | xargs kill -9
# fi

if [[ $count -lt 1 ]]; then
    /usr/bin/php /var/www/html/api/artisan PayRepay >> $log_path &
fi;
