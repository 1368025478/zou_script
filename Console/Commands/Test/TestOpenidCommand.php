<?php
/**
 * Created by PhpStorm.
 * User: zyp
 * Date: 19-6-27
 * Time: 下午4:52
 */

namespace App\Commands\Test;

use Illuminate\Support\Facades\Redis;
use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Illuminate\Database\Capsule\Manager as Db;
use Inhere\Console\Util\Helper;
use Service\CommonService;
use Service\GzhUserService;
use Service\LogService;
use Service\RedisService;

class TestOpenidCommand extends Command
{
    protected static $name = 'test_openid';

    protected static $description = 'testn测试脚本';

    // 启用协程运行
    protected static $coroutine = false;


    protected function execute($input, $output)
    {


        $redis = RedisService::connectionRedis();
        $log = LogService::init($this->getCommandName());
        $prefix = config('redis.prefix');
        $log->info('-----------同步公众号openid脚本开始----------');

        $next_openid = 'xxxxxxxxx-Kp7qaOhw';
        $i = 0;
        while (1) {
            $tag_user_list = GzhUserService::user_list($next_openid);

            $data = json_decode($tag_user_list, true);
            if ($data['total'] == $i) {
                exit;
            }
            if (!isset($data['data'])) {
                exit;
            }
            $user_list = $data['data']['openid'];
            $next_openid = $data['next_openid'];
            $log->info('next_openid:' . $next_openid);
            foreach ($user_list as $k => $v) {
                $i += 1;
                $openid = $v;
                $unionid = GzhUserService::get_unionid($openid);
                if ($unionid) {
                    $log->info('gzh_openid:' . $openid);
                    $log->info('unionid:' . $unionid);

                    $uid = Db::table('user')->where('unionid', $unionid)->value('id');
                    if($uid){

                        $log->info('uid:' . $uid);


                        if (!Db::table('attention')->where('a_unionid', $unionid)->where('a_gzh_type', 1)->exists()) {
                            $log->info('attention表不存在');
                            $insert_data = [
                                'a_unionid' => $unionid,
                                'a_gzh_openid' => $openid,
                                'a_gzh_type' => 1,
                                'a_time' => time()-3600
                            ];
                            $ins = Db::table('attention')->insert($insert_data);
                            if(!$ins){
                                $log->info('attention 添加失败');
                            }

                        }else{
                            $log->info('attention 已存在');
                        }
                    }else{
                        $log->info('uid 不存在跳出');
                        continue;
                    }
                } else {
                    $log->info('uniounionid 不存在跳出');
                    continue;
                }


            }
            $log->info('count:'.$i);
        }


    }


}