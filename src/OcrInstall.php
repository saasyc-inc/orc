<?php

namespace Yiche\Ocr;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OcrInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yiche:ocr-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ocr百度身份证识别记录表安装';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sql = dirname(__DIR__) . '/database/sql/ocr_idcard.sql';
        $model = new \Yiche\Ocr\Models\OcrIdcard();
        $tableName = $model->getTable();
        if (Schema::hasTable($tableName)) {
            $this->line("{$tableName}表已经创建");
        } else {
            DB::unprepared(file_get_contents($sql));
        }

        $sql2 = dirname(__DIR__) . '/database/sql/ocr_vehicle_license.sql';
        $model = new \Yiche\Ocr\Models\OcrVehicleLicense();
        $tableName = $model->getTable();
        if (Schema::hasTable($tableName)) {
            $this->line("{$tableName}表已经创建");
        } else {
            DB::unprepared(file_get_contents($sql2));
        }

        $sql3 = dirname(__DIR__) . '/database/sql/ocr_business_license.sql';
        $model = new \Yiche\Ocr\Models\OcrBusinessLicense();
        $tableName = $model->getTable();
        if (Schema::hasTable($tableName)) {
            $this->line("{$tableName}表已经创建");
        } else {
            DB::unprepared(file_get_contents($sql3));
        }

        $sql4 = dirname(__DIR__) . '/database/sql/ocr_bankcard.sql';
        $model = new \Yiche\Ocr\Models\OcrBankcard();
        $tableName = $model->getTable();
        if (Schema::hasTable($tableName)) {
            $this->line("{$tableName}表已经创建");
        } else {
            DB::unprepared(file_get_contents($sql4));
        }
    }
}
