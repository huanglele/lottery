<?php
/**
 * Author: huanglele
 * Date: 2016/7/18
 * Time: 下午 03:05
 * Description:
 */

function readConf($k,$nocache = false){
    $r = S($k);
    if(!($r) || $nocache){
        $r =  M('config')->where(array('key'=>$k))->getField('value');
        S($k,$r);
    }
    return $r;
}

function writeConf($k,$v){
    $M = M('config');
    S($k,$v);
    if($M->where(array('key'=>$k))->find()){
        $M->where(array('key'=>$k))->setField('value',$v);
    }else{
        $data['key'] = $k;
        $data['value'] = $v;
        $M->add($data);
    }
}

/**
 * @param string $timestr 需要格式化的时间戳
 * @return bool|string 格式化后时间字符串
 */
function Mydate($timestr=''){
    if(''==$timestr){
        $timestr = time();
    }
    if($timestr==0){
        return '';
    }else {
        return date('Y-m-d H:i', $timestr);
    }
}

/**
 * @param $openId
 */
function getWxUserInfo($openId)
{
    $access = getWxAccessToken();
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access&openid=$openId&lang=zh_CN";
    $res = myCurl($url);
    $info = json_decode($res,true);
    return $info;
}


/**
 * @return mixed 微信凭证
 */
function getWxAccessToken(){
    $Wx = C('Wx');
    $appId = $Wx['AppID'];
    $appSec = $Wx['AppSecret'];
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSec";
    $res = myCurl($url);
    $data = json_decode($res,true);
    $token = $data['access_token'];
    return $token;
}


function myCurl($url,$data=false)
{
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
    if($data){
        curl_setopt_array($ch,$data);
    }
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    //运行curl，结果以jason形式返回
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}





/**
 * ----------------------------------------------------------
 * 字符串截取，支持中文和其它编码
 * @static
 * @access public +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * +----------------------------------------------------------
 * @return string +----------------------------------------------------------
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix) return $slice . "…";
    return $slice;
}