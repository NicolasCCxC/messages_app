<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddInitialModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        collect(Module::MODULES)->each(function ($module) {
            Module::insert([
                'id' => Str::uuid()->toString(),
                'name' => $module,
                'description' => $this->getPort($module),
                'state' => false,
                'token' => self::token[$module]
            ]);
        });
    }

    const token = [
        Module::SECURITY => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJ2OWQwS0UwQU9vY0pLd1RvIiwic2VydmljZSI6IlNFQ1VSSVRZIn0.gcJen7wOug1-xfQdxvFaYCuYImwW3b1xfR6LL9EWPHU',
        Module::INVOICE => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJKM2hTUnVuTExFNGUzaEVyIiwic2VydmljZSI6IklOVk9JQ0UifQ.BmVx2dFgYE0VsDgH9Oh-_6PBtMHArNpyz1gMppf6K64',
        Module::UTILS => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJsYkZySDNxSTRTOHpmTmFsIiwic2VydmljZSI6IlVUSUxTIn0.aq99HMDHmTQiE7SMvP2ElxgIpE36_oUrAOCUCYepzCg',
        Module::INVENTORY => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJRNHpEMFZWdFU5clhPQVBHIiwic2VydmljZSI6IklOVkVOVE9SWSJ9.WYp4iR0tCymqydXa1eP9g04PFRlVmIowdhFYOlygDv0',
        Module::BUCKET => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJsQm1ObU1XWTFpMmp2OWI2Iiwic2VydmljZSI6IkJVQ0tFVCJ9.c_AUXLo1Sn5AscPofhK8-Q2BzpWAMNgs750xmy8ejrQ',
        Module::QUALIFICATION => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiI2eTJ4Y01XbzVFV2FqTnRhIiwic2VydmljZSI6IlFVQUxJRklDQVRJT04ifQ.3w90bgW2vI7qBnSapapzvS3KvD1OsqLgBmLEA3Em7rs',
        Module::BINNACLE => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiI4NVBiRmp3cWcwSzljbDd3Iiwic2VydmljZSI6IkJJTk5BQ0xFIn0.hDAYSELAzkxNQ-5Z7ygxWhXN503w_HniOGG1zyXsrMQ',
        Module::NOTIFICATION => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJjdTJYUmpKUGVvQ3k4OTVnIiwic2VydmljZSI6Ik5PVElGSUNBVElPTiJ9.-hPZu42Y7RrrViMvmcsovp0CbPWr5I4AbHGtnussb64',
        Module::WEBSITE => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiIyeVpDaE5WeExjQ3VCVDdvIiwic2VydmljZSI6IldFQlNJVEUifQ.X29FQgDxE6xihNfiw6Evfg6EkXF6hlM22AeFBS4tA6U',
        Module::ACCOUNTING => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJlSjNsMUpmQk5YYlgzdnUwIiwic2VydmljZSI6IkFDQ09VTlRJTkcifQ.X0Gw5uTLCvpDPed6A6bMJdRzL62TAJcV0G39sG12Ky0',
        Module::SHOPPING => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJJcVQ0ZDVpcWloNDUySGoyIiwic2VydmljZSI6IlNIT1BQSU5HIn0.Secs5A2V9RpB0cmF7Nu-KZZHJbwFZBS4Eq0x42kWUw0',
        Module::ELECTRONIC_INVOICE => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJ4T1Q2MXF2NXFYNVhJcE9QIiwic2VydmljZSI6IkVMRUNUUk9OSUNfSU5WT0lDRSJ9.N3mZmwkJS2RFE2FC2UccR1o0st9yD3Lldr8DJm97jbU',
        Module::DOMAIN => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJ2OWQwS0UwQU9vY0pLd1RvIiwic2VydmljZSI6IkRPTUFJTiJ9.-nf6UOz3doaLJUdmA4_r0Qok96mOwgcE0KPRozEU1Ik',
        Module::PAYS => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiJKM2hTUnVuTExFNGUzaEVyIiwic2VydmljZSI6IlBBWVMifQ.cSIm6JCFSV-3hVnMKo0zR_WjeFuKO_6Oc-BAXz4Rh9k'
    ];

    function getPort($module): string
    {
        switch ($module) {
            case Module::SECURITY:
                return 'https://develop-api-security.famiefi.com';
            case Module::INVOICE:
                return 'http://develop-api-invoice.famiefi.com';
            case Module::UTILS:
                return 'http://develop-api-utils.famiefi.com';
            case Module::ACCOUNTING:
                return 'http://develop-api-accounting.famiefi.com';
            case Module::BINNACLE:
                return 'http://develop-api-binnacle.famiefi.com';
            case Module::BUCKET:
                return 'http://develop-api-bucket.famiefi.com';
            case Module::NOTIFICATION:
                return 'http://develop-api-notification.famiefi.com';
            case Module::SHOPPING:
                return 'http://develop-api-shopping.famiefi.com';
            case Module::WEBSITE:
                return 'http://develop-api-website.famiefi.com';
            case Module::INVENTORY:
                return 'http://develop-api-inventory.famiefi.com';
            case Module::QUALIFICATION:
                return 'http://develop-api-qualification.famiefi.com';
            case Module::ELECTRONIC_INVOICE:
                return 'http://develop-api-electronic-invoice.famiefi.com';
            case Module::DOMAIN:
                return 'http://develop-api-domain.famiefi.com';
            case Module::PAYS:
                return 'http://develop-api-pays.famiefi.com';
            default:
                return 'http://famiefi.com';
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('modules')->truncate();
    }
}
