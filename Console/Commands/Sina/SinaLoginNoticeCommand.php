<?php
/**
 * Created by PhpStorm.
 * User: zyp
 * Date: 19-6-27
 * Time: 下午4:52
 */

namespace App\Commands\Sina;

use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Illuminate\Database\Capsule\Manager as Db;
use Inhere\Console\Util\Helper;
use Service\CommonService;
use Service\GzhUserService;
use Service\LogService;
use Service\RedisService;

class SinaLoginNoticeCommand extends Command
{
    protected static $name = 'sina_login';

    protected static $description = 'crontab微博登陆';

    // 启用协程运行
    protected static $coroutine = false;

    /**
     * do execute command
     * example php ybTask example --param='hello,this param'
     * @param Input $input
     * @param Output $output
     * @return int|mixed
     */
    protected function execute($input, $output)
    {
        $redis = RedisService::connectionRedis();
        $log = LogService::init($this->getCommandName());
        $prefix = config('redis.prefix');
        $log->info('-------------微博脚本执行开始----------');

        $account_list = Db::table('sina_account')
            ->where('is_use', 1)
            ->where('status', '<>',3)
            ->get();
        $account_list = obj2array($account_list);
        $redis->del($prefix.'sinna_cookie');
        foreach ($account_list as $key => $value){

            $udata = $value;
            $path =  '/data/webapp/zou/zou_script/SinaPy/';

            $cmd = 'python ' . $path . 'auto_login.py user=' . $udata['account'] . ' pass=' . $udata['password'];

            var_dump($cmd);
            system($cmd);
            $cookie_path = $path . 'cookie.txt';
            $cookie = getWeiboCookie($cookie_path);

            var_dump('------------------------'.$cookie.'-----------------------');
            $log->error('------------------------'.$cookie.'-----------------------');
            if($cookie == 'null'){
                Db::table('sina_account')->where('id', $udata['id'])->update(['status' => 3]);
            }
            $res = upload_weibo("https://imgs.t.sinajs.cn/t6/style/images/global_nav/WB_logo-x2.png?id=1404211047727", false, $cookie);
            $data = json_decode($res, true);

            var_dump($data);
            if (isset($data['data']['pics']['pic_1']['pid']) && !empty($data['data']['pics']['pic_1']['pid'])) {
                Db::table('sina_account')->where('id', $udata['id'])->update(['status' => 2]);

                $redis->zAdd($prefix.'sinna_cookie', $udata['id'], $cookie);
            } else {
                Db::table('sina_account')->where('id', $udata['id'])->update(['status' => 4]);
                Db::table('sina_account')->where('id', $udata['id'])->increment('error_count', 1);
            }

        }

        $log->info('-------------微博脚本执行结束----------');
    }


}