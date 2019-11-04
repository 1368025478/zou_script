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

class SinaImagesNoticeCommand extends Command
{
    protected static $name = 'sina_images';

    protected static $description = 'Nohup微博图片上传';

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
        $log->info('-------------微博上传图片脚本执行开始----------');

        while (1) {
            if (!$redis->exists($prefix . 'weibo_images_list')) {
                sleep(3);
                continue;
            }
            #获取需要自动参与的抽奖列表
            $data = $redis->brpop($prefix . 'weibo_images_list', 0);
            if ($data[1]) {

                $img_data = json_decode($data[1], true);

                switch ($img_data['type']) {
                    case 1:

                        if (isset($img_data['id'])) {
                            $path = $prefix . 'lottery:' . $img_data['id'];

                            if($img_data['cover']){
                                $str = self::upload_webo($img_data['cover'], $path,'-cover', 'cover_weibo');
                            }else{
                                $str = null;
                            }

                            if($img_data['award_imgs']){
                                $str2 = self::upload_webo($img_data['award_imgs'], $path,'-max', 'award_imgs_weibo');
                            }else{
                                $str2 = null;
                            }

                            if(Db::table('lottery_weibo_images')->where('id', $img_data['id'])->exists()){

                                Db::table('lottery_weibo_images')->where('id', $img_data['id'])->update(
                                    ['cover_weibo' => $str, 'award_imgs_weibo' => $str2]
                                );

                            }else{
                                $insert_data = [
                                    'id' => $img_data['id'],
                                    'cover_weibo' => $str,
                                    'award_imgs_weibo' => $str2,
                                    'create_time' => time()
                                ];
                                Db::table('lottery_weibo_images')->where('id', $img_data['id'])->insert($insert_data);
                            }
                        }
                        break;
                }


            }
        }
        $log->info('-------------微博上传图片脚本执行结束----------');
    }


    /**
     * @param $images_list
     * @param $redis_key
     * @param string $houzui
     * @param string $hash_key
     * @return bool|string
     *
     * 上传微博图片
     *
     */
    public static function upload_webo($images_list, $redis_key,  $houzui = '-cover', $hash_key = 'cover_webo')
    {
        $redis = RedisService::connectionRedis();
        $prefix = config('redis.prefix');

        //cover图片处理
        $cover_str = '';
        $cover_images = explode(',', $images_list);
        foreach ($cover_images as $value) {

            if(strstr($value, 'sinaimg') || strstr($value, 'yzcdn') ||strstr($value, $houzui)){
                $cover_str .= $value.',';
                continue;
            }
            if ($value == 'http://wx1.sinaimg.cn/large/006D9DgPgy1g5pwpphyysj30ku0af74m.jpg' ||
                $value == 'https://wx2.sinaimg.cn/large/8e796ae9gy1fys05lh6dfj20ku0af0st.jpg') {
                $cover_str .= $value.',';
                continue;
            }
            $max_count = $redis->zCard($prefix . 'sinna_cookie');
            $rand_cookie = rand(0, $max_count - 1);
            $cookie = $redis->zRange($prefix . 'sinna_cookie', $rand_cookie, $rand_cookie);
            if (isset($cookie[0])) {
                $cookie = $cookie[0];
                $res = upload_weibo($value . $houzui, false, $cookie);
                $data = json_decode($res, true);
                var_dump($data);
                if (isset($data['data']['pics']['pic_1']['pid']) && !empty($data['data']['pics']['pic_1']['pid'])) {
                    $img = "https://ww2.sinaimg.cn/large/" . $data['data']['pics']['pic_1']['pid'];
                    $cover_str .= $img . ',';
                } else {
                    $cover_str .= $value . $houzui . ',';
                }

            } else {
                $cover_str .= $value . $houzui . ',';
            }
        }

        if ($cover_str != '') {

            if( substr($cover_str, strlen($cover_str) - 1, strlen($cover_str)) == ','){
                $cover_str = substr($cover_str, 0, strlen($cover_str) - 1);
            }
            $redis->hSet($redis_key, $hash_key, $cover_str);
            return $cover_str;
        }
    }

}