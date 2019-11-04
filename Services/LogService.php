<?php

namespace Service;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;


/**
 * Class RedisService
 * @package Service
 * 提供关于日志封装的服务
 */
class LogService extends CommonService
{


    /**
     * @param $event
     * @return Logger
     * @throws \Exception
     * 初始化日志
     */
    public static function init($event)
    {

        $log = new Logger($event);
        $level = config('log.level');

        $path = dirname(dirname(__FILE__));

        switch ($level) {
            case 100:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'), Logger::DEBUG);
                break;
            case 200:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'), Logger::INFO);
                break;
            case 250:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'), Logger::NOTICE);
                break;
            case 300:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'), Logger::WARNING);
                break;
            case 400:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'), Logger::ERROR);
                break;
            case 500:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'), Logger::CRITICAL);
                break;
            case 550:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'), Logger::ALERT);
                break;
            case 600:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'), Logger::EMERGENCY);
                break;
            default:
                $log->pushHandler(new StreamHandler($path . '/Log/' . $event . '/' . date('Y-m-d',
                        time()) . '/' . $event . '.log'));
                break;
        }
        $log->pushHandler(new FirePHPHandler());
        return $log;
    }


}