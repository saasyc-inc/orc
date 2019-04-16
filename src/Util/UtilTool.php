<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 9:45
 */

namespace Yiche\Ocr\Util;

use Yiche\Config\Models\SapiConfig;

class UtilTool
{
    /**
     * 格式化时间,阿里云oss需要
     */
    public static function gmtToiso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    /**
     * 接口
     * @param int $code
     * @param array $data
     * @param string $message
     * @return mixed
     */
    public static function output($code = 200, $data = [], $message = "")
    {
        $data = [
            'success' => ($code == 200) ? true : false,
            'error_no' => $code,
            'error_msg' => !empty($message) ? $message : ($code == 200 ? "请求成功" : "请求失败"),
            'result' => !empty($data) ? $data : new \stdClass(),
        ];
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


    /**
     * 生成文件file_id
     * @param $id
     * @return string
     */
    public static function createFileId($id)
    {
        $sn_left = microtime(1) . (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : rand(1, 9999));
        if ($id > 1000) {
            //方法1,每秒不超过100w的并发数,
            $sn_right = str_pad($id % 1000000, 6, '0', STR_PAD_LEFT);
        } else {
            //方法2,每毫秒不超过1000个并发
            $str = str_pad(microtime(1) * 1000 % 1000, 3, '0', STR_PAD_LEFT);
            $sn_right = $str . str_pad($id % 1000, 3, '0', STR_PAD_LEFT);
        }
        return md5($sn_left . $sn_right);
    }

    /**
     * 获取文件类型,针对阿里云oss
     * @param $type 文件后缀
     * @return string
     */
    public static function getFileType($type)
    {
        $type_array = array(
            'pdf' => array('pdf'), //PDF
            'image' => array('bmp', 'jpg', 'jpeg', 'gif', 'png'), //IMAGE
            'zip' => array('zip', 'rar', '7z', 'gz'), //ZIP
            'video' => array('mp4', 'rmvb', 'mkv', 'avi', 'mov', 'mpg', '3gp', 'm4v', 'flv'),//VIDEO
            'excel' => array('xls', 'xlsx'), //EXCEL
            'word' => array('doc', 'docx'),//word
            'others' => array('others')
        );
        $map = array();
        foreach ($type_array as $k => $v) {
            foreach ($v as $k2 => $v2) {
                $map[$v2] = array(
                    'type' => $k,
                    'sub_type' => $k2,
                );
            }
        }
        return !empty($map[$type]) ? $map[$type]['type'] : 'others';
    }

    /**
     * 获取百度ocr配置
     * @param string $key
     * @return string
     */
    public static function getOcrConfig($key = "baidu.ocr")
    {
        $sapi_config = new SapiConfig();
        $config_value = $sapi_config->getConfigValue($key);
        if (!empty($config_value)) {
            $jsonObj = json_decode(json_encode($config_value,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $jsonObj = new \stdClass();
        }
        return $jsonObj;
    }
}