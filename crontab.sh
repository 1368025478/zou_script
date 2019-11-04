#!/bin/bash
start=/www/wwwroot/hd_choujiang/hdcj_sciprt/script

#微博登陆脚本
result=$(crontab -l | grep "sina_login")
if [[ "$result" == "" ]]
then
  crontab -l > conf && echo "*/10 * * * * /data/app/php/bin/php $start sina_login" >> conf && crontab conf && rm -f conf
fi
