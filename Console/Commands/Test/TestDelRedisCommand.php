<?php
/**
 * Created by PhpStorm.
 * User: zyp
 * Date: 19-6-27
 * Time: 下午4:52
 */

namespace App\Commands\Test;

use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Illuminate\Database\Capsule\Manager as Db;
use Inhere\Console\Util\Helper;
use PDO;
use Service\CommonService;
use Service\LogService;
use Service\RedisService;

class TestDelRedisCommand extends Command
{
    protected static $name = 'test_del_redis';

    protected static $description = 'testn测试脚本';

    // 启用协程运行
    protected static $coroutine = false;


    protected function execute($input, $output)
    {

        $redis = RedisService::connectionRedis();
        $log = LogService::init($this->getCommandName());
        $prefix = config('redis.prefix');
        $log->info('-----------删除脚本开始----------');
        ini_set('memory_limit','2048M');

        $this->scan($redis, 'test:del_list:*', $log);

        $log->info('-----------删除脚本结束----------');


    }


    public function scan($redis, $key, $log){

        $redis = RedisService::connectionRedis();

        $iterator = null;
        $redis->setOption($redis::OPT_SCAN, $redis::SCAN_RETRY);
        $it = NULL;
        var_dump($key);
        /* phpredis will retry the SCAN command if empty results are returned from the
           server, so no empty results check is required. */
        $del_count = 0;
        $ext_count = 0;

        while ($arr_keys = $redis->scan($it, $key, 10000)) {
            foreach ($arr_keys as $str_key) {
                var_dump ("Here is a key: $str_key");

                $score_list = $redis->zRevRange($str_key, 0, 0);
                if(isset($score_list[0])){
                    $score = $redis->zScore($str_key, $score_list[0]);
                    if($score < 1562774400){
                        $redis->del($str_key);
                        $del_count++;
                        var_dump('del:'.$del_count);

                        $log->info('--删除redis--'. $str_key);
                    }else{
                        if($redis->ttl($str_key) == -1){
                            $redis->expireAt($str_key, time()+604800);
                            $ext_count++;
                            var_dump('exp:'.$ext_count);
                            $log->info('--设置过期时间--'. $str_key);
                        };
                    }
                }else{
                    var_dump('no get_score'.json_encode($score_list));
                }

            }
        }
        var_dump("No more keys to scan!");
    }


}