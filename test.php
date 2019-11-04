<?php

usleep(0);


//
//$arr = [
//    0 => ['lname' =>1],
//    1 => ['lname' =>3],
//    2 => ['lname' =>4],
//];
//
//unset($arr[1]);
//
//
//var_dump($arr);
//
//$arr = array_values($arr);
//var_dump($arr);
//$data = file_get_contents("https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIRVWANia8uZIWxnoibyq9cy5rS3WcsdKwa755aQQm2h8TiaRn1RajMaPgnuVxXNvfyyXicmO2PWZmwlg/132");
////
////$data2 = file_get_contents("https://wx.qlogo.cn/mmopen/vi_32/rmAeuv0zaACUzXqImkdS9xMxx5u0J7cA6T0w48zhibdiaibRlSR04DNJHREzXIYIPfxFWHicbFMFfI35HtqicgG16ag/132");
//$str = md5($data);
////$str2 = md5($data2);
////
//var_dump($str);
//var_dump($str2);
//
//$str3 = '"[{"lname":"Lakers","num":"1","receive_type":"0","gid":0},{"lname":"LBJ","num":"2","receive_type":0,"gid":0},{"lname":"\u6e56\u4eba","num":"3","receive_type":0,"gid":0}]"';
//
//var_dump(json_decode($str3, true));
//$path = dirname(__FILE__) . '/SinaPy/';
//
//var_dump($path);
//
//$cookie_path = $path . 'cookie.txt';
//
//$cookies = file_get_contents($cookie_path);
//var_dump($cookies);
//
//if($cookies == 'null'){
//    return $cookies;
//}
//$cookieArr = explode("\n", $cookies);
//if ($cookieArr) {
//    foreach ($cookieArr as $k => $v) {
//
//        $pos = strpos($v, 'Set-Cookie3');
//        if ($pos !== false) {
//            $cv = explode(":", $v);
//            $cookieStr .= $cv[1] . ';';
//        }
//
//    }
//}
//
//
//return $cookieStr;