<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectDebtorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospect_debtors', function (Blueprint $table) {
            $table->id();
            $table->dateTime('booking_date');
            $table->string('booking_number');
            $table->string('fullname', 60);
            $table->string('phone_number', 15);
            $table->string('email', 60);
            $table->string('address', 200);
            $table->string('license_plate', 20);
            $table->string('vehicle_type', 150);
            $table->string('application_status', 10)->default(\App\Enums\Status::new);
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('prospect_debtors');
    }
}
