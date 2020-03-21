<?php

namespace App\Resource\Tool;

use Uccu\DmcatTool\Tool\LocalConfig as Config;
use Uccu\DmcatTool\Tool\AJAX;
use App\Resource\Model\VisitModel;
use App\Resource\Model\IpTrackerModel;

class Func
{

    /**
     *
     * 验证是否是合法的邮箱 
     * 做了下修改
     * @source  http://www.jquerycn.cn/a_10460
     * @return boolean 
     *
     */

    static function validate_email($email)
    {

        $exp = "#^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$#";

        if (preg_match($exp, $email)) {

            return checkdnsrr(array_pop(explode("@", $email)), "MX") ? true : false;
        } else {

            return false;
        }
    }


    /**
     *
     * 随机字符串
     * @author pzcat
     * @param number $count 字符串的长度   
     * @param boolean $s 是否只输出小写字母
     * @return string 字符串
     *
     */

    public static function randWord($count = 1, $s = 0)
    {
        $rand = 'ABCDEFGJIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
        $rand2 = '1234567890abcdefghijklmnopqrstuvwxyz';
        $rand3 = '1234567890';
        $o = '';
        for ($i = 0; $i < $count; $i++) {
            if (!$s) $o .= $rand[rand(0, 61)];
            elseif ($s == 2) $o .= $rand2[rand(0, 35)];
            elseif ($s == 3) $o .= $rand3[rand(0, 9)];
        }
        return $o;
    }


    /* 上传图片 */
    static function uploadFiles($name = null, $width = 0, $height = 0, $cut = 0)
    {
        if (!$_FILES) return [];

        $paths = [];
        $upn = $upa = [];
        foreach ($_FILES as $k => $file) {
            $upn[] = $k;
            if (!$name || $k == $name) {
                $upa[] = $k;
                $paths[$k] = self::uploadPic($file['tmp_name'], 0, $width, $height, $cut);
            }
        }
        $data['upn'] = implode(',', $upn);
        $data['upa'] = implode(',', $upa);
        $data['succ'] = ($name ? $paths[$name] : $paths) ? 1 : 0;
        $data['ctime'] = TIME_NOW;

        // LogUploadModel::copyMutiInstance()->set($data)->add();
        return $name ? $paths[$name] : $paths;
    }
    /* 处理上传图片 */
    static function uploadPic($tmp_name, $type = 0, $width = 0, $height = 0, $cut = 0)
    {
        if (!$tmp_name) AJAX::error('上传失败,无法获取缓存路径');
        $arr = getimagesize($tmp_name);
        /* 判断图片格式 */
        switch ($arr[2]) {
            case 3:
                $img = imagecreatefrompng($tmp_name);
                imagesavealpha($img, true);
                break;
            case 2:
                $img = imagecreatefromjpeg($tmp_name);
                break;
            case 1:
                $img = imagecreatefromgif($tmp_name);
                break;
            default:
                AJAX::error('解析图片失败');  //非jpg/png/gif 强制退出程序
                break;
        }
        $picRoot = PUBLIC_ROOT . 'pic';
        if ($type === 'md5') {
            $md5 = md5_file($tmp_name);
            $folder = substr($md5, 0, 2);
            $folderRoot = $picRoot . '/' . $folder;
            if (!is_dir($folderRoot))
                !mkdir($folderRoot, 0777, true) && AJAX::error('文件夹权限不足，无法创建文件！');
            $folderRoot .= '/';
            $src = $folderRoot . $md5 . '.jpg';
            $path = $folder . '/' . $md5 . '.jpg';
        } else {
            $folder = DATE_TODAY;
            $folderRoot = $picRoot . '/' . $folder;
            if (!is_dir($folderRoot))
                !mkdir($folderRoot, 0777, true) && AJAX::error('文件夹权限不足，无法创建文件！');
            $folderRoot .= '/';
            $time = date('H.i.s.') . self::randWord(6, 3);
            $src = $folderRoot . $time . '.jpg';
            $path = $folder . '/' . $time . '.jpg';
        }
        $width0 = $arr[0];
        $height0 = $arr[1];
        $width = $width ? $width : $width0;
        // var_dump($height);die();
        $height = $height ? $height : $height0;

        $image = imagecreatetruecolor($width, $height);
        $color = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $color);
        if ($cut == 0)
            imagecopyresampled($image, $img, 0, 0, 0, 0, $width, $height, $width0, $height0);

        imagejpeg($image, $src, 75);
        imagedestroy($image);
        return $path;
    }
    public static function add_zero($number, $total)
    {
        $string = strval($number);
        $len = strlen($string);
        for ($i = $len; $i < $total; $i++) {
            $string = '0' . $string;
        }
        return $string;
    }

    /* 判断是否通过ssl访问 */
    static function is_SSL()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    }
    /* 返回全图片网址 */
    static function fullPicAddr($path)
    {
        return self::fullAddr('pic/' . $path);
    }
    /* 返回全网址 */
    static function fullAddr($path)
    {
        return (self::is_SSL() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/' . $path;
    }

    /* 模拟curl */
    public static function curl($url, $data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $z = curl_exec($ch);
        return $json = json_decode($z);
    }

    public static function localExec($path)
    {

        $exe = 'php ' . PUBLIC_ROOT . 'index.php ';
        exec($exe . $path, $arr);

        if ($arr) {
            return json_decode($arr[0]);
        }

        return false;
    }

    static public function time_wcalculate($time)
    {
        return floor($time % 3600) . ':' . self::add_zero(floor(floor($time % 3600) / 60), 2);
    }
    static function duringZcalculate($second = 0)
    {

        $data = new \stdClass;

        $data->seconds = 0;
        $data->hours = 0;

        $rate = 1800;


        $data->seconds = ceil($second / $rate) * $rate;
        $data->hours = number_format($data->seconds / 3600, 1, '.', '');

        return $data;
    }

    /**
     *
     * AES转码/解码
     * @author pzcat
     * @param string $str 需要转码/解码的字符串
     * @param string $key 需要的秘钥
     * @return string 转码/解码后的字符串
     *
     */

    /*AES转码*/
    public static function aes_encode($str, $key = null)
    {
        if (!$key) $key = Config::get('AES_SECRECT_KEY');
        $key = substr(md5($key), 0, -16);
        $iv = substr(md5($key), 16);
        $strEncrypted = openssl_encrypt($str, "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($strEncrypted);
    }
    /*AES解码*/
    public static function aes_decode($str, $key = null)
    {
        if (!$key) $key = Config::get('AES_SECRECT_KEY');
        $key = substr(md5($key), 0, -16);
        $iv = substr(md5($key), 16);
        $str = base64_decode($str);
        return openssl_decrypt($str, "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
    }


    /**
     *
     * 人性化时间转换
     * @author pzcat
     * @param number $time 10位数字的时间戳
     * 
     * @return string 
     *      今日
     *      当小于1m时      xx秒前
     *      当小于1h时      xx分xx秒前
     *      当小于1d时      xx时xx分前
     *      昨日           昨日xx时xx分
     *      其他           xxxx-xx-xx
     *
     */

    static public function time_calculate($time)
    {

        if ($time >= TIME_TODAY) {

            $time = TIME_NOW - $time;

            $second = $time % 60;
            $time = ($time - $second) / 60;
            if (!$time) return $second . '秒前';

            $minute = $time % 60;
            $hour = ($time - $minute) / 60;
            if (!$hour) return $minute . '分' . ($second ? ($second < 10 ? '0' : '') . $second . '秒' : '') . '前';

            return $hour . '时' . ($minute ? ($minute < 10 ? '0' : '') . $minute . '分' : '') . '前';
        } elseif ($time >= TIME_YESTERDAY) {

            return '昨日' . date('H时i分', $time);
        } else {

            return date('Y-m-d', $time);
        }
    }

    static public function visit_log()
    {

        $ip = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        $ip = ($ip) ? $ip : $_SERVER["REMOTE_ADDR"];

        $data['ip'] = $ip;
        $data['date'] = date('Y-m-d');
        $data['time'] = date('H:i:s');
        $data['referer'] = $_SERVER['HTTP_REFERER'];
        $data['url'] = REQUEST_PATH;
        if ($ip && !in_array($ip, ['127.0.0.1', 'localhost', '::1', 'fe80::1%lo0',])) {
            $model = IpTrackerModel::getInstance();
            $ipM = $model->find($ip);
            if (!$ipM) {
                $json = self::zCurl('http://ip-api.com/json/' . $ip);
                $json = json_decode($json);
                $data2['ip'] = $ip;
                if ($json->status == 'success') {
                    $data2['country'] = $json->country;
                    $data2['city'] = $json->city;
                    $data2['isp'] = $json->isp;
                    $data2['_as'] = $json->as;
                    $data2['country_code'] = $json->countryCode;
                    $data2['org'] = $json->org;
                    $data2['region'] = $json->region;
                    $data2['region_name'] = $json->regionName;
                    $data2['timezone'] = $json->timezone;
                    $data2['zip'] = $json->zip;
                }
                $model->set($data2)->add();
            }
            $add = VisitModel::getInstance()->set($data)->add()->lastInsertId;
            return $add;
        }
        return false;
    }

    static public function zCurl($url, $postData = [])
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $json = curl_exec($ch);
        curl_close($ch);
        return $json;
    }
}
