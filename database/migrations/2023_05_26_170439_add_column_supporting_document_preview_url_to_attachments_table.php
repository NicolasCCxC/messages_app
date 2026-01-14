<?php

use App\Infrastructure\Formulation\BucketHelper;
use App\Infrastructure\Persistence\AttachmentEloquent;
use App\Models\Attachment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AddColumnSupportingDocumentPreviewUrlToAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('supporting_document_preview_url')->nullable()->after('preview_url');
        });

        $attachments = Attachment::where('name', 'logo')->get();
        $attachments->map(function ($attachment) {
            $data = BucketHelper::getUrl($attachment['bucket_id']);
            if ($data) {
                try {
                    $attachmentEloquent = new AttachmentEloquent();
                    $attachmentEloquent->updateCreate($data, $attachment['company_id'], $attachment['name']);
                } catch (Throwable $e) {
                    Log::warning($data);
                    Log::error($e);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn('supporting_document_preview_url');
        });
    }
}
