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

class TestPushCommand extends Command
{
    protected static $name = 'test_push';

    protected static $description = 'test测试推送脚本';

    // 启用协程运行
    protected static $coroutine = false;


    protected function execute($input, $output)
    {

        $redis = RedisService::connectionRedis();
        $log = LogService::init($this->getCommandName());
        $prefix = config('redis.prefix');
        $log->info('-----------测试推送脚本开始----------');




        $log->info('-----------测试推送脚本结束----------');


    }





}