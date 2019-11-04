<?php

namespace Service;

/**
 * Class RedisService
 * @package Service
 * 提供关于redis封装的服务
 */
class RedisService extends CommonService
{

    /**
     * @return \Redis
     * 初始化连接redis
     */
    public static function connectionRedis(){
        $config = config('redis');
        $REDIS_HOST =  $config['host'];
        $REDIS_AUTH =  $config['password'];
        $REDIS_PORT =  $config['port'];
        $redis = new \Redis();
        $redis -> pconnect($REDIS_HOST,$REDIS_PORT);
        $redis -> auth($REDIS_AUTH);
        return $redis;
    }



}