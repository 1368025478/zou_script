<?php

namespace Service;

use Illuminate\Database\Capsule\Manager as Db;


/**
 * Class RedisService
 * @package Service
 * 提供公共服务
 */
class CommonService
{


    /**
     * @param int $uid 用户主键id
     * @param string $template_id 模版id
     * @param array $mag 模版消息数组
     * @param string $page 发送通知的小程序路径
     * @param string $big_keyword 需要加大字体 示例：keyword1.DATA
     * @return bool
     * 发送小程序模版消息通用服务接口
     */
    public static function service_msg_send(
        $uid,
        $template_id,
        $push_list,
        $msg,
        $page = 'pages/index/index',
        $big_keyword = null,
        $my_params = null
    ) {
        $redis = RedisService::connectionRedis();
        $prefix = config('redis.prefix');

        $formid = $redis->zRevRange($prefix . 'formid_list:' . $uid, 0, 0);
        if (!isset($formid[0])) {
            return false;
        }
        $msg_data = [
            'touser' => self::get_userinfo($uid, 'openid'),
            'template_id' => $template_id,
            'page' => $page,
            'form_id' => $formid[0],
            'data' => $msg,
        ];
        $redis->zRem($prefix . 'formid_list:' . $uid, $formid[0]);
        if ($big_keyword) {
            $msg_data['emphasis_keyword'] = $big_keyword;
        }
        if($my_params){
            $msg_data = array_merge($msg_data, $my_params);
        }

        if ($push_list) {
            $res = $redis->lpush($push_list, json_encode($msg_data));
            if($res){
                return true;
            }else{
                return false;
            }
        }


        $access_token = $redis->get($prefix . 'access_token');
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $access_token;
        $result = json_decode(http_post($url, json_encode($msg_data)), true);
        $time = date('Y-m-d', time());
        $push_count_path = $prefix . 'service_push_count:' . $time;
        if ($result['errcode'] == 0) {
            $redis->hIncrBy($push_count_path, 'count', 1);
            $redis->hIncrBy($push_count_path, 'push_num', -1);
        } else {
            if ($result['errcode'] == 41028 || $result['errcode'] == 41029) {
                $redis->hIncrBy($push_count_path, 'error_num_formid', 1);
            } else {
                $redis->hIncrBy($push_count_path, 'error_num', 1);
            }
            return false;
        }
        return true;
    }


    /**
     * @param $openid
     * @param $template_id
     * @param $push_list
     * @param $data
     * @param string $page
     * @param string $appid
     * @return bool
     * 处理数据放入推送队列或直接发送（公众号）
     */
    public static function push_notice_list(
        $openid,
        $template_id,
        $push_list,
        $data,
        $page = 'pages/index/index',
        $appid = ''
    ) {
        $redis = RedisService::connectionRedis();
        $prefix = config('redis.prefix');
        $msg_data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'miniprogram' => ['appid' => $appid, 'pagepath' => $page],
            'data' => $data
        ];
        //有队列push队列 没有直接发送
        if ($push_list) {
            $redis->lpush($push_list, json_encode($msg_data));
        } else {
            $access_token = $redis->get($prefix . 'gzh_access_token');
            $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
            $res = Json_post($url, json_encode($msg_data));
            $res = json_decode($res, true);
            var_dump($res);
            if($res['errmsg'] != 'ok'){
                return false;
            }

        }

        return true;
    }



}

