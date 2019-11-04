#!/bin/bash

#脚本文件启动路径
start=/data/webapp/zou/zou_script/script
#ouput输出文件路径
output_page=/data/webapp/zou/zou_script/output



#推送脚本
alive=`ps aux|grep "$start push_xcx_notice" |grep -v grep|wc -l`
if [ $alive -eq 0 ]
then
nohup  /data/app/php/bin/php $start push_xcx_notice key_list=test_list push_speed = 100000 > $output_page/push_xcx_notice.output 2>&1 &
fi



#微博图片上传脚本
alive=`ps aux|grep "$start sina_images" |grep -v grep|wc -l`
if [ $alive -eq 0 ]
then
nohup  /data/app/php/bin/php $start sina_images > $output_page/sina_images.output 2>&1 &
fi