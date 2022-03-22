<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboxsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inboxs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('title', 60);
            $table->string('content');
            $table->string('type', 10);
            $table->boolean('read')->default(false);
            $table->string('icon')->default('https://file.sitama.co.id/storage/pms/icons/inbox.jpg');
            $table->bigInteger('company_id');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inboxs');
    }
}
