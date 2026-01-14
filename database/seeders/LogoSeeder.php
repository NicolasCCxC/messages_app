<?php

namespace Database\Seeders;

use App\Models\Attachment;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Seeder;
use App\Models\Company;
use Illuminate\Support\Str;

class LogoSeeder extends Seeder
{

    public function run()
    {

        $response = Http::withToken(env('BUCKET_TOKEN'))
        ->attach(
            'file',
            file_get_contents(storage_path('test.png')),
            'logo.png')
        ->post(
            env('BUCKET_SERVICE_URL').'/bucket/upload-file',
            [
                'company_id' => Company::COMPANY_CCXC,
                'folder' => 'logo',
                'service' => 'BUCKET',
                'data' => '{
                    "company_id": "83e80ae5-affc-32b4-b11d-b4cab371c48b"
                }',
            ]
        )
        ->json();

        Attachment::create([
            'id' => Str::uuid()->toString(),
            'name' => 'logo',
            'bucket_id' => $response["data"]["id"],
            'company_id' => Company::COMPANY_CCXC,
            'preview_url' => $response["data"]["url"],
        ]);
    }
}
