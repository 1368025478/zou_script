<?php
/**
 * Created by PhpStorm.
 * User: zyp
 * Date: 19-6-27
 * Time: 下午4:52
 */

namespace App\Commands\Robot;

use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Illuminate\Database\Capsule\Manager as Db;
use Inhere\Console\Util\Helper;
use Service\CommonService;
use Service\LogService;
use Service\RedisService;

class RobotCommand extends Command
{
    protected static $name = 'robot_change';

    protected static $description = 'crontab机器人修改脚本';

    // 启用协程运行
    protected static $coroutine = false;


    protected function execute($input, $output)
    {

        $redis = RedisService::connectionRedis();
        $log = LogService::init($this->getCommandName());
        $prefix = config('redis.prefix');
        $log->info('-----------更换机器人脚本开始----------');

        $time = time();
        ini_set('memory_limit','2048M');



        $login_time = $time - 1814400;
        $add_time = $time - 2592000;

        $robot_list = self::get_range(500001, 520000, 10000);
        $men_list = self::get_user_list(1, $login_time,$add_time);
        $women_list = self::get_user_list(2, $login_time,$add_time);

        $change_list = array_merge($men_list, $women_list);


        foreach ($robot_list as $key => $value) {
            $uid = $value;
            $update_data = $change_list[$key];

            $update_data['id'] = $uid;
            $res = $redis->hMSet($prefix . 'userinfo:' . $uid, $update_data);

            var_dump($res);
            var_dump($prefix . 'userinfo:' . $uid);
            $log->info($prefix . 'userinfo:' . $uid);
        }

        $log->info('-----------更换机器人脚本结束----------');


    }

    /**
     * @param $start
     * @param $end
     * @return array
     * 获取不重复的随机数
     */
    public static function get_range($start, $end, $number)
    {
        $numbers = range($start, $end - 1);

        shuffle($numbers);

        $num = $number;

        $result = array_slice($numbers, 0, $num);
        return $result;
    }


    /**
     * @param $sex
     * @param $login_time
     * @return array
     * 获取随机性别用户5000人
     */
    public static function get_user_list($sex, $login_time,$add_time)
    {
        $robot_men = Db::table('user')
            ->where('register_time', '>=', $add_time)
            ->where('login_time', '<=', $login_time)
            ->where('sex', $sex)
            ->limit(20000)
            ->orderBy('id', 'desc')
            ->select('uname', 'avatar')
            ->get();
        $robot_men = obj2array($robot_men);

        //fee9458c29cdccf10af7ec01155dc7f0

        try{
            foreach ($robot_men as $k => $v){
                if($v['avatar']){

                    var_dump($k);
                    var_dump($v['avatar']);

                    //获取微信图片二进制数据 进行md5 判断头像是否过期
//                    var_dump(md5(http_get($v['avatar'])));
                    @$str = md5(http_get($v['avatar']));
                    var_dump($str);

                    if($str == 'fee9458c29cdccf10af7ec01155dc7f0'){
                        unset($robot_men[$k]);
                    }


                }else{
                    unset($robot_men[$k]);
                }

            }

        }catch (Exception $e){

        }


        $robot_men = array_values($robot_men);

        $range = self::get_range(0, 10000, 10000);
        $uname_list = [];
        $avatar_list = [];
        foreach ($range as $key => $value) {
            if (($key + 1) % 2 == 0) {
                $avatar_list[]['avatar'] = $robot_men[$value]['avatar'];
            } else {
                $uname_list[]['uname'] = $robot_men[$value]['uname'];
            }
        }
        foreach ($uname_list as $k => $v) {
            $uname_list[$k] = array_merge($uname_list[$k], $avatar_list[$k]);
        }

        return $uname_list;
    }

}