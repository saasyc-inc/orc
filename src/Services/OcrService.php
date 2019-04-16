<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 11:34
 */

namespace Yiche\Ocr\Services;


use Illuminate\Support\Facades\DB;
use Monolog\Logger;


use Yiche\Ocr\Util\UtilTool;

class OcrService
{
    public $config;
    private $client;

    public function __construct()
    {
        $this->config = UtilTool::getOcrConfig();
        if (empty($this->config)) {
            throw new \Exception("缺少百度OCR配置,请检测!" . PHP_EOL);
        }
        $this->client = new \AipOcr($this->config->APP_ID, $this->config->API_KEY, $this->config->SECRET_KEY);
    }

    /**
     * 请求识别逻辑
     * @param $request
     */
    public function idcard($param)
    {
        $ret = $log = [];
        $flag = false;
        if ($param['type'] == 1) {//身份证正面
            $idCardSide = "front";
        } elseif ($param['type'] == 2) {//身份证反面
            $idCardSide = "back";
        }
        $log['idCardSide'] = $idCardSide;
        //下载文件
        $image = $this->downFile($param['fileUrl']);
        // 如果有可选参数
        $options = array();
        $options["detect_direction"] = "true";
        $options["detect_risk"] = "false";
        $image = base64_decode($image);
        // 带参数调用身份证识别
        try {
            $raw = $this->client->idcard($image, $idCardSide, $options);
            $json = $raw;
            if ((isset($json['image_status']) && ($json['image_status'] != "normal")) || isset($json['error_code'])) {
                //百度身份证识别数据错误
                $flag = false;
            } else {
                $ret = $this->formatIDCardData($json);
            }
            $param['response'] = $raw;
        } catch (\Exception $exception) {
            $flag = false;
            $param['response'] = $exception->getMessage();
            //throw new \Exception("识别错误");
        }
        //写入log
        $newLog = array_merge($log, $param, $ret);
        $this->saveLog($newLog, $flag);
        return ($flag == true) ? $ret : false;
    }

    /**
     * 远程文件下载
     * @param $fileUrl
     * @return bool|string
     */
    public function downFile($fileUrl)
    {
        if (trim($fileUrl) == '') {
            return false;
        }
        // curl下载文件
        $ch = curl_init();
        $timeout = 3600;
        curl_setopt($ch, CURLOPT_URL, $fileUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $img = curl_exec($ch);
        curl_close($ch);
        return $img;
    }

    /*
     * 身份证识别接口数据,格式化处理,兼容极速数据
     */
    protected function formatIDCardData($json)
    {
        $ret = [];
        $jsonArr = $json;
        $result = $jsonArr['words_result'];
        $ret['address'] = isset($result['住址']) ? $result['住址']['words'] : "";
        $ret['birth'] = isset($result['出生']) ? date('Y-m-d', strtotime($result['出生']['words'])) : "";
        $ret['name'] = isset($result['姓名']) ? $result['姓名']['words'] : "";
        $ret['idcard'] = isset($result['公民身份号码']) ? $result['公民身份号码']['words'] : "";
        $ret['sex'] = isset($result['性别']) ? $result['性别']['words'] : "";
        $ret['nation'] = isset($result['民族']) ? $result['民族']['words'] : "";
        $ret['retain'] = "";
        $ret['issueorg'] = isset($result['签发机关']) ? $result['签发机关']['words'] : "";;
        $ret['startdate'] = isset($result['签发日期']) ? date('Y-m-d', strtotime($result['签发日期']['words'])) : "";;
        $ret['enddate'] = isset($result['失效日期']) ? (($result['失效日期']['words'] !== "长期") ? date('Y-m-d',
            strtotime($result['失效日期']['words'])) : $result['失效日期']['words']) : "";
        return $ret;
    }

    /**
     * 写入请求log
     * @param $row
     * @param bool $success
     * @return bool
     */
    protected function saveLog($param, $success = true)
    {
        $log = [
            'app_id' => $this->config->APP_ID,
            'app_key' => $this->config->APP_KEY,
            'secret_key' => $this->config->SECRET_KEY ?? "",
            'idcard' => $param['idcard'] ?? "",
            'fileUrl' => $param['fileUrl'] ?? "",
            'address' => $param['address'] ?? "",
            'birth' => $param['birth'] ?? "",
            'name' => $param['name'] ?? "",
            'idcard' => $param['idcard'] ?? "",
            'sex' => $param['sex'] ?? "",
            'nation' => $param['nation'] ?? "",
            'retain' => $param['retain'] ?? "",
            'issueorg' => $param['issueorg'] ?? "",
            'startdate' => $param['startdate'] ?? "",
            'enddate' => $param['enddate'] ?? "",
            'idCardSide' => $param['idCardSide'] ?? "",
            'from_ip' => $param['from_ip'] ?? "",
            'status' => $success == true ? 1 : 2,
            'request' => json_encode($param['request'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'response' => json_encode($param['response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'from_ip' => $param['fromIp'],
            'created_at' => date("Y-m-d H:i:s", time()),
        ];

        DB::table('ocr_idcard')->insert($log);

        return true;
    }
}