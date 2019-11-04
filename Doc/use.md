## 使用说明

lottery_crantab.sh 定义所有脚本的定时任务

protect_open_script 定义所有脚本的常驻任务


一：脚本文件命名规范

```php
namespace App\Commands\SyncData; #命名空间 目录都是驼峰命名

use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Illuminate\Database\Capsule\Manager as Db; #引入db库
use Inhere\Console\Util\Helper;
use Service\CommonService;
use Service\LogService; #引入日志
use Service\RedisService; #引入redis

class SyncDataCommand extends Command
{
    // 脚本执行命令
    protected static $name = 'sync_data';

    // 脚本描述 抬头声明脚本类型 crontab定时 nohup常驻
    protected static $description = 'crontab同步抽奖参与人数脚本';

    // 启用协程运行
    protected static $coroutine = false;

    /**
     * do execute command
     * @param Input $input
     * @param Output $output
     * @return int|mixed
     */
    protected function execute($input, $output)
    {
        //初始化所有应用
        $redis = RedisService::connectionRedis();
        $log = LogService::init($this->getCommandName());
        $prefix = config('redis.prefix');
        $log->info('-----------同步抽奖参与人数脚本开始----------');
        //脚本业务逻辑
        Db::table('lottery')->where('lstatus', 0)->select('id')->get();
  
        $log->info('-----------同步抽奖参与人数脚本结束----------');

    }
}
```

二：目录结构说明
![group-command-list](https://qny-cj.9w9.com/9dda120190703154839.png)

三：详细使用方式

参考php-console.wiki

四：crontab.sh写法

```php
#!/bin/bash
#定义脚本启动路径
start=/www/wwwroot/hd_choujiang/hdcj_sciprt/script

#10分钟切换精品福利脚本（脚本注释）
result=$(crontab -l | grep "jpfl_change")
if [[ "$result" == "" ]]
then
  crontab -l > conf && echo "*/10 * * * * php $start jpfl_change" >> conf && crontab conf && rm -f conf
fi

```

五：nohup.sh 写法

```php
#!/bin/bash
start=/www/wwwroot/hd_choujiang/hdcj_sciprt/script
output_page=/www/wwwroot/hd_choujiang/hdcj_sciprt/output
old_page=/www/wwwroot/hd_choujiang/hdcj_sciprt/z_old/open_lottery

#自动参与抽奖脚本
alive=`ps aux|grep lottery_auto|grep -v grep|wc -l`
if [ $alive -eq 0 ]
then
nohup  php $start lottery_auto > $output_page/lottery_auto.output 2>&1 &
fi

```

