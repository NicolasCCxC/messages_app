<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateAttachmentsTable extends Migration
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
            $this->updateFieldWithRoot('preview_url', $replacement);
            $this->updateFieldWithRoot('supporting_document_preview_url', $replacement);
        }
    }

    public function updateFieldWithRoot(string $field, string $replacement)
    {
        $attachments = DB::table('attachments')
            ->where($field, 'ilike', '%/famiefi/%')
            ->orderBy('id')
            ->cursor();
        foreach ($attachments as $attachment) {
            $newUrl = str_replace('/famiefi/', '/' . trim($replacement, '/') . '/', $attachment->$field);
            DB::table('attachments')
                ->where('id', $attachment->id)
                ->update([$field => $newUrl]);
        }
    }
}
