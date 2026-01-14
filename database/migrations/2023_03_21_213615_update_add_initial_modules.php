<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAddInitialModules extends Migration
{
    private $modules;

    public function __construct()
    {
        $this->modules = [
            [
                'id' => 'ed40cbeb-3406-4367-8501-5884c251687c',
                'name' => Module::PAYROLL,
                'description' => 'http://qa-api-payroll.famiefi.com',
                'state' => false,
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJKM2hTUnVuTExFNGUzaEVzIiwic2VydmljZSI6IlBBWVJPTEwifQ.fDncl7BtKqGW4DZ8LZU8C7KV4Sz_PjX-Vfo3wh6XFJE'
            ],
            [
                'id' => '6b7cbcef-f9be-40bf-a744-462760971099',
                'name' => Module::ELECTRONIC_PAYROLL,
                'description' => 'http://qa-api-electronic-payroll.famiefi.com',
                'state' => false,
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJKM2hTUnVuTExFNGUzaEVEIiwic2VydmljZSI6IkVMRUNUUk9OSUNfUEFZUk9MTCJ9.yBTZlG0ONZSwsvcDSgdgISsPAOs0psDDqBiTGT0B7LQ'
            ] 
        ];
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Module::query()->insert($this->modules);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Module::query()->findMany(collect($this->modules)->pluck('id'))->each(fn($module) => $module->delete());
    }
}
