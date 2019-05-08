<?php

namespace Yiche\Ocr\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yiche\Ocr\Services\OcrService;
use Yiche\Ocr\Util\UtilTool;

class OcrController extends Controller
{

    public $config;

    public function __construct()
    {
        $this->config = UtilTool::getOcrConfig();
        if (empty($this->config)) {
            throw new \Exception("缺少百度OCR配置,请检测!" . PHP_EOL);
        }
    }


    /**
     * 身份证识别
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function idcard(Request $request)
    {
        $front = $request->input("front", "");
        $back = $request->input("back", "");
        $discern_back = $request->input('discern_back', 0);
        $ocrService = new OcrService();

        if ($discern_back == 1) {//两张都识别
            if (empty($front) || empty($back)) {
                return UtilTool::output(505, null, "缺少身份证正面和反面文件地址URL");
            } else {
                //正面
                $frontData = $ocrService->idcard(['fileUrl' => $front, "type" => 1]);
                //反面
                $backData = $ocrService->idcard(['fileUrl' => $back, "type" => 2]);

                $result = $this->formatData($frontData, $backData, 1);
            }
        } elseif ($discern_back == 2) {//只识别背面
            if (empty($back)) {
                return UtilTool::output(505, null, "缺少身份证反面文件地址URL");
            } else {
                $param = [
                    //反面
                    'fileUrl' => $back,
                    "type" => 2,
                ];
                $backData = $ocrService->idcard($param);
                $result = $this->formatData([], $backData, 2);
            }
        } else {//默认 只识别正面
            if (empty($front)) {
                return UtilTool::output(505, null, "缺少身份证正面文件地址URL");
            } else {
                $param = [
                    //正面
                    'fileUrl' => $front,
                    "type" => 1,
                ];
                $frontData = $ocrService->idcard($param);
                $result = $this->formatData($frontData, [], 0);
            }
        }

        return UtilTool::output(200, $result, "请求成功");
    }


    /**
     * 输出数据格式化
     * @param $front
     * @param $back
     * @param $type
     * @return array
     */
    public function formatData($front, $back, $type)
    {
        $newresult = [
            "discern_back" => $type,
            "front" => [
                'name' => $front['name'] ?? "--",
                'sex' => $front['sex'] ?? "--",
                'nation' => $front['nation'] ?? "--",
                'birth' => $front['birth'] ?? "--",
                'address' => $front['address'] ?? "--",
                'idcard' => $front['idcard'] ?? "--"
            ],
            "back" => [
                'authority' => $back['issueorg'] ?? '--',
                'valid_start' => $back['startdate'] ?? '--',
                'valid_end' => $back['enddate'] ?? '--'
            ],
        ];

        return $newresult;
    }
}