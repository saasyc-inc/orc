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


    public function idcard()
    {
        $param = [
            //正面
            'fileUrl'=>"https://yiche-static.oss-cn-hangzhou.aliyuncs.com/anjie/uploads/image/20190416/79b2ab6ef10cd0ac154ab21bdeac8a47.jpg",
            //反面
            //"fileUrl"=>"https://yiche-static.oss-cn-hangzhou.aliyuncs.com/anjie/uploads/image/20190416/93ca6a2a8cbb0119a4fab9e2a8108c2f.jpg",
            "type"=>1,
            "from_ip"=>"127.0.0.1"
        ];
        $ocrService = new OcrService();
        $result = $ocrService->idcard($param);
        dd($result);
    }

}