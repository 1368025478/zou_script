<?php
/**
 * Created by PhpStorm.
 * User: zyp
 * Date: 19-6-27
 * Time: 下午4:52
 */

namespace App\Commands\Lottery;

use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Illuminate\Database\Capsule\Manager as Db;
use Inhere\Console\Util\Helper;
use Service\CommonService;
use Service\LogService;
use Service\RedisService;
use Service\StatisticsService;

class PushNoticeCommand extends Command
{
    protected static $name = 'push_xcx_notice';

    protected static $description = 'nohup推送通知';

    // 启用协程运行
    protected static $coroutine = false;

    /**
     * do execute command
     * example php ybTask example --param='hello,this param'
     * @param Input $input
     * @param Output $output
     * @return int|mixed
     * 小程序通知推送脚本
     */
    protected function execute($input, $output)
    {
        $redis = RedisService::connectionRedis();
        $log = LogService::init($this->getCommandName());
        $prefix = config('redis.prefix');
        $log->info('-------------推送通知开始----------');

        //获取推送队列名称
        $key_list = $prefix . $input->get('key_list', 'notice_list');

        //初始化推送url
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=';

        //获取推送速度值 毫秒为单位
        $push_speed = $input->get('push_speed', 0);


        while (1) {
            if (!$redis->exists($key_list)) {
                sleep(3);
                continue;
            }

            $data = $redis->brpop($key_list, 0);
            if ($data[1]) {
                $access_token = $redis->get($prefix . 'access_token');


                if ($push_speed > 0) {
                    usleep((int)$push_speed);
                }


                $result = json_decode(http_post($url . $access_token, $data[1]), true);

                if ($result['errmsg'] != 'ok') {
                    $log->error('推送失败', $result);
                } else {
                    $log->info('推送成功');
                }

            }
        }
        //调用推送任务
        $log->info('-------------推送通知结束----------');
    }


}