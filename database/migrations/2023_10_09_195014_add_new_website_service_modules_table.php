<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

class AddNewWebsiteServiceModulesTable extends Migration
{
    private $id = '9f201acb-3cc1-4e18-9cf4-04d9130510ee';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Module::insert([
            [
                'id' => $this->id,
                'name' => 'WEBISTE_NODE',
                'description' => 'http://develop-api-website-node.famiefi.com/',
                'state' => false,
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJxYS1hcGktc2VjdXJpdHkuZmFtaWVmaS5jb20iLCJpYXQiOjE2MzE4OTQ2MzQsImV4cCI6MTY2MzQ1NDcxMSwibmJmIjoxNjMxODk0NjM0LCJqdGkiOiIyeVpDaE5WeExjQ3VCVDdvIiwic2VydmljZSI6IldFQlNJVEUifQ.X29FQgDxE6xihNfiw6Evfg6EkXF6hlM22AeFBS4tA6U'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Module::find($this->id)->delete();
    }
}
