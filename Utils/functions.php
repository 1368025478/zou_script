<?php
/**
 * User: zyp
 * Date: 19-6-27
 * Time: 5:07 pm
 */

if (!function_exists('outError')) {
    function outError(\Exception $ex)
    {
//        $errorMsg = '[code] ' . $ex->getCode() . PHP_EOL;
        $errorMsg = '[error] ' . $ex->getMessage() . PHP_EOL;
        $errorMsg .= '[pos] ' . $ex->getFile() . ' on line ' . $ex->getLine() . PHP_EOL;
//        $errorMsg .= '[previous] ' . var_export($ex->getPrevious(), true) . PHP_EOL;
        $errorMsg .= '[Trace] ' . $ex->getTraceAsString() . PHP_EOL;

        return $errorMsg;
    }
}


/**
 * @param $key
 * @return mixed
 * 获取系统配置
 */
function config($key)
{
    $key = explode('.', $key);
    $path = dirname(dirname(__FILE__));
    $key_path = $key[0] . '.php';
    $config = include($path . '/Config/' . $key_path);
    if (isset($key[1])) {
        return $config[$key[1]];
    } else {
        return $config;
    }
}

/**
 * @param $arr
 * @return mixed
 * 对象转数组
 */
function obj2array($arr)
{
    return json_decode(json_encode($arr), true);
}


/**
 * @param int time
 * @return boolean
 * 判断是否是今天，昨天，0表示今天，1表示昨天
 */
function today($time)
{
    // 今天最大时间
    $todayLast = strtotime(date('Y-m-d 23:59:59'));
    $agoTime = $todayLast - $time;
    return floor($agoTime / 86400);
}


/**
 * @param $url
 * @param $jsonStr
 * @return bool|string
 * json格式提交
 */
function Json_post($url, $jsonStr)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;',
            'Content-Length:' . strlen($jsonStr)
        )
    );
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}


/**
 * 发送https请求，需要开启php_curl
 * @param unknown_type $url
 * @param unknown_type $data
 */
function http_post($url, $data = null, $headers = array())
{
    $curl = curl_init();
    if (count($headers) >= 1) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * @param $num
 * @return string
 *
 */
function _idToString($num)
{
    $baseChar = '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz';
    $str = '';
    while ($num != 0) {
        $tmp = $num % 32;
        $str .= $baseChar[$tmp];
        $num = intval($num / 32);
    }
    return $str;
}

/**
 * @desc  im:十机制数转换成三十二进制数
 * @param (string)$char 三十二进制数
 * return 返回：十进制数
 */
function encry($id)
{
    $id += 123456789;
    $str = str_pad($id, 10, '0', STR_PAD_LEFT);
    $num1 = intval($str[0] . $str[2] . $str[6] . $str[9]);
    $num2 = intval($str[1] . $str[3] . $str[4] . $str[5] . $str[7] . $str[8]);
    $str1 = $str2 = '';
    $str1 = _idToString($num1);
    $str1 = strrev($str1);
    $str2 = _idToString($num2);
    $str2 = strrev($str2);
    return str_pad($str1, 3, 'U', STR_PAD_RIGHT) . str_pad($str2, 4, 'L', STR_PAD_RIGHT);
}

/**
 * @desc  im:三十二进制数转换成十机制数
 * @param (string)$char 三十二进制数
 * return 返回：十进制数
 */
function decrypt($str)
{
    $str1 = trim(substr($str, 0, 3), 'U');
    $str2 = trim(substr($str, 3, 4), 'L');
    $num1 = _stringToId($str1);
    $num2 = _stringToId($str2);
    $str1 = str_pad($num1, 4, '0', STR_PAD_LEFT);
    $str2 = str_pad($num2, 6, '0', STR_PAD_LEFT);
    $id = ltrim($str1[0] . $str2[0] . $str1[1] . $str2[1] . $str2[2] . $str2[3] . $str1[2] . $str2[4] . $str2[5] . $str1[3],
        '0');
    $id -= 123456789;
    return $id;
}

/**
 * 公用方法字符串转数字
 * @param $str
 * @return float|int|string
 */
function _stringToId($str)
{
    $baseChar = '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz';
    //转换为数组
    $charArr = array_flip(str_split($baseChar));
    $num = 0;
    for ($i = 0; $i <= strlen($str) - 1; $i++) {
        $linshi = substr($str, $i, 1);
        if (!isset($charArr[$linshi])) {
            return '';
        }
        $num += $charArr[$linshi] * pow(32, strlen($str) - $i - 1);
    }

    return $num;
}


function request_post($url = '', $param = '')
{
    if (empty($url) || empty($param)) {
        return false;
    }
    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Accept:application/vnd.lumen.v3+json"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}



/**
 * 请求接口返回内容
 * @param string $url [请求的URL地址]
 * @param string $params [请求的参数]
 * @param int $ipost [是否采用POST形式]
 * @return  string
 */
function juhecurl($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT,
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === false) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}





/**
 * 发送https请求，需要开启php_curl
 * @param unknown_type $url
 * @param unknown_type $data
 */
function http_get($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    $output = curl_exec($curl);
    if (curl_errno($curl)) {
        return 'Error: ' . curl_errno($curl) . curl_error($curl);
    }
    curl_close($curl);
    return $output;
}


/**
 * @param $url
 * @param $jsonStr
 * @return bool|string
 * post请求  json提交方式
 */
function Json_query($url, $jsonStr)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;',
            'Content-Length:' . strlen($jsonStr)
        )
    );
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}


function getWeiboCookie($cookieFile = './cookie.txt')
{
    $cookieStr = '';
    $cookies = file_get_contents($cookieFile);
    if($cookies == 'null'){
        return $cookies;
    }
    $cookieArr = explode("\n", $cookies);
    if ($cookieArr) {
        foreach ($cookieArr as $k => $v) {

            $pos = strpos($v, 'Set-Cookie3');
            if ($pos !== false) {
                $cv = explode(":", $v);
                $cookieStr .= $cv[1] . ';';
            }

        }
    }
    return $cookieStr;
}


function upload_weibo($file, $multipart = true, $cookie = '')
{
    $url = 'http://picupload.service.weibo.com/interface/pic_upload.php'
        . '?mime=image%2Fjpeg&data=base64&url=0&markpos=1&logo=&nick=0&marks=1&app=miniblog';
    if ($multipart) {
        $url .= '&cb=http://weibo.com/aj/static/upimgback.html?_wv=5&callback=STK_ijax_' . time();
        if (class_exists('CURLFile')) {     // php 5.5
            $post['pic1'] = new \CURLFile(realpath($file));
        } else {
            $post['pic1'] = '@' . realpath($file);
        }
    } else {
        $post['b64_data'] = base64_encode(file_get_contents($file));
    }
    // Curl提交
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_VERBOSE => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("Cookie: $cookie"),
        CURLOPT_POSTFIELDS => $post,
    ));
    $output = curl_exec($ch);
    curl_close($ch);
    // 正则表达式提取返回结果中的json数据
    preg_match('/({.*)/i', $output, $match);
    if (!isset($match[1])) {
        return '';
    }
    return $match[1];
}


/**
 * 通过curl上传图片
 * @param string path  文件绝对路径
 * @param string url 上传地址
 * @return [type]
 */
function curl_img($url, $path)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
    $data = array('file' => new \CURLFile(realpath($path)));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, "TEST");
    $result = curl_exec($curl);
    $error = curl_error($curl);
    return $result;
}


function public_path(){
    $path = dirname(dirname(__FILE__)).'/Public/';
    return $path;
}