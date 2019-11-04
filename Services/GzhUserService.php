<?php

namespace Service;


/**
 * 提供列表服务相关的统一处理方法
 * Class AskService
 * @package App\Http\Service
 */
class GzhUserService
{
    /**
     * @return mixed
     * 获取公众号token
     */
    static public function get_token_gzh()
    {
        $redis = RedisService::connectionRedis();
        $prefix = config('redis.prefix');
        return $redis->get($prefix.'gzh_access_token');
    }

    /**
     * 获取标签
     */
    static public function tag_list()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token=' . self::get_token_gzh();
        $result = http_get($url);
        return $result;
    }


    /**
     * 获取标签用户列表
     */
    static public function tag_user_list($tag_id, $next_openid = '')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=' . self::get_token_gzh();
        $post_data = [
            'tagid' => $tag_id,
            'next_openid' => $next_openid,
        ];
        $result = Json_query($url, json_encode($post_data));
        return $result;
    }

    /**
     * @param string $newxt_openid
     * @return bool|string
     * 获取公众号用户列表
     */
    static public function user_list($newxt_openid = '')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . self::get_token_gzh() . '&next_openid=' . $newxt_openid;
        $result = http_get($url);
        return $result;
    }

    /**
     * @param string $begin_openid
     * @return bool|string
     * 获取公众号黑名单列表
     */
    static public function black_user_list($begin_openid = '')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist?access_token=' . self::get_token_gzh();
        $post_data = [
            'begin_openid' => $begin_openid,
        ];
        $result = Json_query($url, json_encode($post_data));
        return $result;
    }


    /**
     * @param $openid
     * @return bool
     * 获取公众号的unionid
     */
    public static function get_unionid($openid)
    {
        $response = http_get('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . self::get_token_gzh() . '&openid=' . $openid);
        $response = json_decode($response, true);
        if (isset($response['unionid'])) {
            return $response['unionid'];
        }else{
            return false;
        }
    }


    /**
     * @param $openid
     * @return bool
     * 获取用户当前关注状态
     */
    public static function get_subscribe($openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . self::get_token_gzh() .
            '&openid='.$openid.'&lang=zh_CN';

        $result = http_get($url);
        $result = json_decode($result, true);
        if(isset($result['subscribe']) && $result['subscribe'] != 1){
            return false;
        }
        return true;
    }

    /**
     * @param $openid
     * @return bool
     * 获取用户当前关注状态
     */
    public static function get_subscribe2($openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . self::get_token_gzh2() .
            '&openid='.$openid.'&lang=zh_CN';

        $result = http_get($url);
        $result = json_decode($result, true);
        if(isset($result['subscribe']) && $result['subscribe'] != 1){
            return false;
        }
        return true;
    }
}


?>