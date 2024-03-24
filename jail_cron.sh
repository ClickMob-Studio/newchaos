#!/bin/bash
PATH=$PATH:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/snap/bin
while true; do
    begin=`date +%s`
    php /var/www/html/jail_cron.php
    end=`date +%s`
    if [ $(($end - $begin)) -lt 1 ]; then
        sleep $(($begin + 1 - $end))
    fi
done
