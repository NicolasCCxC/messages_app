<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $replacement = config('app.aws_s3_root_directory');
        $appEnv = config('app.env');
        $appDebug = config('app.debug');

        if ($replacement && $appEnv != 'production' && $appDebug != false) {
            $this->updateFieldWithRoot('invoice_pdf', $replacement);
            $this->updateFieldWithRoot('invoice_credit_note_pdf', $replacement);
        }
    }

    public function updateFieldWithRoot(string $field, string $replacement)
    {
        $memberships = DB::table('memberships')
            ->where($field, 'ilike', '%/famiefi/%')
            ->orderBy('id')
            ->cursor();
        foreach ($memberships as $membership) {
            $newUrl = str_replace('/famiefi/', '/' . trim($replacement, '/') . '/', $membership->$field);
            DB::table('memberships')
                ->where('id', $membership->id)
                ->update([$field => $newUrl]);
        }
    }
}
