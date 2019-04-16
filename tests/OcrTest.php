<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 14:58
 */

namespace Tests;

use Illuminate\Http\Request;
use League\Flysystem\Util;
use PHPUnit\Framework\TestCase;
use Yiche\Ocr\Http\Controllers\OcrController;
use Yiche\Ocr\Services\OcrService;
use Yiche\Ocr\Util\UtilTool;

//require_once dirname(__DIR__).'/vendor/autoload.php';


class OcrTest extends TestCase
{
    public function testOcr()
    {
        //$OcrService = new OcrService();
        //$result = $OcrService->getAssumeRoleRequest();
        //dd($result);
        $this->assertTrue(true);
    }

    //./vendor/bin/phpunit tests/OcrTest.php
    public function testConfig()
    {
        //$request = request();
        //$request->offsetSet('file_type', 'image');
        //$request->offsetSet('project', 'anjie');
        //$controller = new OcrController();
        //$res = $controller->ossconfig();
        //dd($res);

        $config = UtilTool::getOcrConfig();
        dd($config);
    }

}
