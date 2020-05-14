<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 11:34
 */

namespace Yiche\Ocr\Services;


use Illuminate\Support\Facades\DB;
use Yiche\Ocr\Util\UtilTool;


class OcrService
{
    public $config;
    public $client;

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
        $fromIP = !empty($param['from_ip']) ? $param['from_ip'] : $this->getIP();
        $param['from_ip'] = $fromIP;
        $log['idCardSide'] = $idCardSide;

        //下载文件,加上压缩参数
        //$imgUrl = $param['fileUrl'];
        $imgUrl = $param['fileUrl']."?x-oss-process=image/resize,m_lfit,w_800,h_800";
        //下载文件
        $image = $this->downFile($imgUrl);

        // 如果有可选参数
        $options = array();
        $options["detect_direction"] = "true";
        $options["detect_risk"] = "false";
        // 带参数调用身份证识别
        try {
            $raw = $this->client->idcard($image, $idCardSide, $options);
            $json = $raw;
            if ((isset($json['image_status']) && ($json['image_status'] != "normal")) || isset($json['error_code'])) {
                //百度身份证识别数据错误
                $flag = false;
            } else {
                $flag = true;
                $ret = $this->formatIDCardData($json);
            }
            $param['response'] = $raw;
        } catch (\Exception $exception) {
            $flag = false;
            $param['response'] = $exception->getMessage();
            //throw new \Exception("识别错误");
        }
        $param['request'] = array_merge($options, ['idCardSide' => $idCardSide]);
        //写入log
        $newLog = array_merge($log, $param, $ret);
        $this->saveIdcardLog($newLog, $flag);
        return ($flag == true) ? $ret : false;
    }

    public function businessLicense($imageUrl, $detect_direction = 'true', $accuracy = 'normal')
    {
        $isSuccess = false;
        $options = [
            'detect_direction' => $detect_direction,
            'accuracy' => $accuracy,
        ];
        $imageUrl .= '?x-oss-process=image/resize,m_lfit,w_800,h_800';
        $image = $this->downFile($imageUrl);
        $raw = $this->client->businessLicense($image, $options);
        $log = [
            'request' => json_encode($options, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'response' => json_encode($raw, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'app_id' => $this->config->APP_ID,
            'api_key' => $this->config->API_KEY,
            'secret_key' => $this->config->SECRET_KEY ?? "",
            'file_url' => $imageUrl,
            'from_ip' => $this->getIP(),
            'created_at' => date('Y-m-d'),
        ];
        $ret = [];
        if (isset($raw['words_result'])) {
            $isSuccess = true;
            $ret = $this->formatBusinessLicenseData($raw);
        }
        $log['status'] = $isSuccess ? 1 : 2;
        $log = array_merge($log, $ret);
        DB::table('ocr_business_license')->insert($log);
        return $isSuccess ? $ret : $isSuccess;
    }

    public function vehicleLicense($imageUrl, $detect_direction = 'true', $vehicle_license_side = 'front', $unified = 'true')
    {
        $isSuccess = false;
        $options = [
            'detect_direction' => $detect_direction,
            'vehicle_license_side' => $vehicle_license_side,
            'unified' => $unified,
        ];
        $imageUrl .= '?x-oss-process=image/resize,m_lfit,w_800,h_800';
        $image = $this->downFile($imageUrl);
        $raw = $this->client->vehicleLicense($image, $options);
        $log = [
            'request' => json_encode($options, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'response' => json_encode($raw, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'app_id' => $this->config->APP_ID,
            'api_key' => $this->config->API_KEY,
            'secret_key' => $this->config->SECRET_KEY ?? "",
            'from_ip' => $this->getIP(),
            'file_url' => $imageUrl,
            'created_at' => date('Y-m-d'),
        ];
        $ret = [];
        if (isset($raw['data']['words_result'])) {
            $isSuccess = true;
            $ret = $this->formatVehicleLicenseData($raw);
        }
        $log['status'] = $isSuccess ? 1 : 2;
        $log = array_merge($log, $ret);
        DB::table('ocr_vehicle_license')->insert($log);
        return $isSuccess ? $ret : $isSuccess;
    }

    public function bankcard($imageUrl, $detect_direction = 'true')
    {
        $isSuccess = false;
        $options = [
            'detect_direction' => $detect_direction,
        ];
        $imageUrl .= '?x-oss-process=image/resize,m_lfit,w_800,h_800';
        $image = $this->downFile($imageUrl);
        $raw = $this->client->bankcard($image, $options);
        $log = [
            'request' => json_encode($options, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'response' => json_encode($raw, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'app_id' => $this->config->APP_ID,
            'api_key' => $this->config->API_KEY,
            'secret_key' => $this->config->SECRET_KEY ?? "",
            'from_ip' => $this->getIP(),
            'file_url' => $imageUrl,
            'created_at' => date('Y-m-d'),
        ];
        $ret = [];
        if (isset($raw['result'])) {
            $isSuccess = true;
            $ret = $raw['result'];
        }
        $log['status'] = $isSuccess ? 1 : 2;
        $log = array_merge($log, $ret);
        DB::table('ocr_bankcard')->insert($log);
        return $isSuccess ? $ret : $isSuccess;
    }

    /**
     * php获取用户真实 IP
     * 注意这种方式只适用于浏览器访问时
     * @return array|false|string *
     *
     */
    private function getIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $realip = $_SERVER["HTTP_CLIENT_IP"];
                } else {
                    $realip = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';
                }
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else {
                if (getenv("HTTP_CLIENT_IP")) {
                    $realip = getenv("HTTP_CLIENT_IP");
                } else {
                    $realip = getenv("REMOTE_ADDR");
                }
            }
        }
        return $realip;
    }

    /**
     * 远程文件下载
     * @param $fileUrl
     * @return bool|string
     */
    private function downFile($fileUrl)
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
    protected function saveIdcardLog($param, $success = true)
    {
        $log = [
            'app_id' => $this->config->APP_ID,
            'api_key' => $this->config->API_KEY,
            'secret_key' => $this->config->SECRET_KEY ?? "",
            'idcard' => $param['idcard'] ?? "",
            'fileUrl' => $param['fileUrl'] ?? "",
            'address' => $param['address'] ?? "",
            'birth' => $param['birth'] ?? "",
            'name' => $param['name'] ?? "",
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
            'created_at' => date("Y-m-d H:i:s", time()),
        ];

        DB::table('ocr_idcard')->insert($log);

        return true;
    }

    /**
     * @param $raw
     * @return array
     */
    private function formatBusinessLicenseData($raw)
    {
        $result = $raw['words_result'];
        return [
            'company_name' => $result['单位名称']['words'] ?? '',
            'type' => $result['类型']['words'] ?? '',
            'legal_person' => $result['法人']['words'] ?? '',
            'address' => $result['地址']['words'] ?? '',
            'valid_date' => $result['有效期']['words'] ?? '',
            'license_no' => $result['证件编号']['words'] ?? '',
            'social_credit_code' => $result['社会信用代码']['words'] ?? '',
        ];
    }

    private function formatVehicleLicenseData(array $raw)
    {
        $result = $raw['data']['words_result'];
        return [
            'brand' => $result['品牌型号']['words'] ?? '',
            'license_release_date' => $result['发证日期']['words'] ?? '',
            'use_type' => $result['使用性质']['words'] ?? '',
            'engine_no' => $result['发动机号码']['words'] ?? '',
            'plate_number' => $result['号牌号码']['words'] ?? '',
            'owner' => $result['所有人']['words'] ?? '',
            'address' => $result['住址']['words'] ?? '',
            'register_date' => $result['注册日期']['words'] ?? '',
            'vin' => $result['车辆识别代号']['words'] ?? '',
            'car_type' => $result['车辆类型']['words'] ?? '',
        ];
    }
}