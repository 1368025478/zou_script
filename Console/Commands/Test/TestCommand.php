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
use Service\CommonService;
use Service\GzhUserService;
use Service\LogService;
use Service\RedisService;


class TestCommand extends Command
{
    protected static $name = 'test';

    protected static $description = 'testn测试脚本';

    // 启用协程运行
    protected static $coroutine = false;


    protected function execute($input, $output)
    {


        $redis = RedisService::connectionRedis();

        $prefix = config('redis.prefix');


//        $path = "hdcj:customer_user:2019-08-28";
//
//        $data = $redis->zRange($path, 0, -1);
//
//        $i = 0;
//
//        foreach ($data as $key => $value){
//            if(CommonService::get_userinfo($value, 'money') == 0.68){
//                $i++;
//                var_dump($i);
//            };
//        }
//
//
//        var_dump($i);
//




//        $res = GzhUserService::tag_list();
//        $res = GzhUserService::tag_user_list('100');

//        $res = json_decode($res, true);
//        $res = $res['data']['openid'];
//        var_dump($res);
//
//
//        $lotteryid = 453208;
//        $robot_count = 7081;
//        $open_lottery_type = 2;
//        CommonService::add_robot_lottery_list($lotteryid, $robot_count, $open_lottery_type);
//
//        var_dump(1);
//
//        $it = null;
//        $count = 10;
//        do {
//            $keysArr = $redis->sscan($prefix.':join_new_lucky_lottery', $it, '*', $count);
//            if ($keysArr) {
//                foreach ($keysArr as $key) {
//
//                    var_dump($key);
//                    $redis->sAdd($prefix. 'join_new_lucky_lottery', $key);
//                }
//            }
//        } while ($it > 0);


//        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
//        $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;

//        $end_time = time() + 24 * 60 * 60;
//        var_dump($end_time);
//        var_dump(time());
//        $result = Db::table('user_lottery')
//            ->leftJoin('lottery', 'user_lottery.lid', '=', 'lottery.id')
//            ->select('user_lottery.uid', 'lottery.end_time')
//            ->where('user_lottery.uid', 100007)
//            ->where('lottery.lstatus', 0)
//            ->where('lottery.end_time', '>=', $end_time)
//            ->where('lottery.end_time', '<=', $end_time)
//            ->orderBy('user_lottery.time', 'DESC')
//            ->limit(1)
////            ->exists()
//            ->toSql();

//            ->;

//        var_dump($result);exit;

//        $data = Db::select('SELECT `c_lottery`.`id`,
//       `c_lottery`.`user_count`,
//       `c_lottery_push_data`.`push_count`
//  FROM `c_lottery`
//  LEFT JOIN `c_lottery_push_data` ON `c_lottery`.`id`= `c_lottery_push_data`.`id`
// WHERE `c_lottery`.`end_time`>= 1565193600
//   AND `c_lottery`.`end_time`<= 1565279999
//   AND `c_lottery`.`lstatus`>= 1
//   AND `c_lottery`.`user_count`> `c_lottery_push_data`.`push_count`');
//
//        $data = obj2array($data);
//        $data_need = [];
//
//        foreach ($data as $key => $value) {
//            $sucess = (int)($value['push_count'] / $value['user_count'] * 100);
//            if ($sucess < 90) {
//                $temp = ['id' => $value['id']];
//                $data_need[] = $temp;
//            }
//        }
//
//
//        foreach ($data_need as $k => $va){
//            $res = $redis->lPush('hdcj:get_notice_data', $va['id']);
//            var_dump($va['id'].$res);
//        }
//        var_dump($data_need);



//        $url_pre = urlencode("../lotteryDetail/lotteryDetail?msg=1&is_relevance=2&msg_type=2&is_home="
//            . 1 . "&lotteryID=71U47MR");
//        $page = 'pages/index/index?jump=' . $url_pre;
//
//        var_dump($page);
//        $data = Db::select('SELECT `c`.`a_gzh_openid`
//  FROM `c_sync_auto_user` AS `a`
//  LEFT JOIN `c_user` AS `b` ON `a`.`uid`= `b`.`id`
//  LEFT JOIN `c_attention` AS `c` ON `b`.`unionid`= `c`.`a_unionid`
//  WHERE `c`.`a_gzh_type` = 1 AND `c`.`a_del_time` IS NOT NULL');
//
//        $data = obj2array($data);
//
//        $i = 0;
//        foreach ($data as $key => $value){
//            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $redis->get($prefix . 'gzh_access_token') .
//                '&openid='.$value['a_gzh_openid'].'&lang=zh_CN';
//
//            $result = http_get($url);
//            $result = json_decode($result, true);
//            var_dump($key);
////            var_dump($result);
//            if(isset($result['subscribe']) && $result['subscribe'] != 1){
//                $i++;
//                var_dump('未关注+1');
//            }
//        }
//
//        var_dump('未关注总数：'.$i);


//        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
//        $smsConf = array(
//            'key' => 'a7a47bc55e75c662a680f8a0d2a61ccf', //您申请的APPKEY
//            'mobile' => '13713876071', //接受短信的用户手机号码
//            'tpl_id' => '134334', //您申请的短信模板ID，根据实际情况修改
//            'tpl_value' => '' //您设置的模板变量，根据实际情况修改
//        );
//        $content = juhecurl($sendUrl, $smsConf, 1); //请求发送短信

//        $data = $redis->zRangeByScore('hdcj:lottery_user:'.$lid, 1,20000000);
//        foreach ($data as $key => $value){
//            $redis->zAdd('hdcj:lottery_friend_award:'.$lid, time(), $value);
//        }
//        var_dump(count($data));
//        return count($data);

//        $redis->zRangeByScore()

//        $num = 3;
//        $lid = 391564;
//        $arr = array();
//        $i = 0;
//        $max = 1;
//        $friend_award_count = $max+1;
//        while (count($arr) <= $num) {
//            var_dump('当前中奖用户列表总数：'.$friend_award_count);
//            if ($friend_award_count > 0) {
//                $a = rand(0, $max);
//                var_dump('当前随机数'.$a);
//                $uid = $redis->zrange('hdcj:lottery_friend_award:' . $lid, $a, $a);
//                var_dump('随机用户');
//                var_dump($uid);
//                $score = $uid[0];
//                var_dump('随机用户uid：'.$score);
//
//            } else {
//                $max = $redis->zCard('hdcj:lottery_friend_code:' . $lid) - 1;
//                var_dump('充值max数量'.$max);
//                $a = rand(0, $max);
//                $uid = $redis->zrange('hdcj:lottery_friend_code:' . $lid, $a, $a);
//                $score = $redis->zScore('hdcj:lottery_friend_code:' . $lid, $uid[0]);
//            }
//
//
//            if (!$redis->sismember("hdcj:award_user_now:$lid", $score)) {
//                $arr[] = $score;
//                $redis->sadd("hdcj:award_user_now:$lid", $score);
//
//                if($friend_award_count > 0){
//                    $friend_award_count -= 1;
//                    $max -= 1;
//                    $redis->zRem('hdcj:lottery_friend_award:'.$lid, $score);
//                }else{
//                    $zount = $redis->ZCOUNT("hdcj:lottery_friend_code:$lid", $score, $score);
//                    $max = $max - $zount;
//                }
////                17302179 17302291
//                $redis->ZREMRANGEBYSCORE("hdcj:lottery_friend_code:$lid", $score, $score);
//
//
//                $lotttery_code_count = $redis->zCard('hdcj:lottery_friend_code:' . $lid);
//                if ($lotttery_code_count < 1) {
//                    break;
//                }
//            }
//            $i++;
//        }
//        var_dump($arr);
    }




}