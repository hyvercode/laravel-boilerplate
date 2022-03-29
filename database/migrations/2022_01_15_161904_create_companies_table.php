<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('company_code', 6);
            $table->string('company_name', 60);
            $table->string('company_alias', 30);
            $table->string('phone_number', 15)->nullable();
            $table->string('email', 50);
            $table->bigInteger('province_id')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->bigInteger('business_id')->nullable();
            $table->integer('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_person', 50)->nullable();
            $table->string('contact_number', 15)->nullable();
            $table->string('npwp_no', 30)->nullable();
            $table->string('npwp_path')->nullable();
            $table->string('siup_no', 30)->nullable();
            $table->string('siup_path')->nullable();
            $table->string('image')->nullable();
            $table->string('coordinate')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('companies');
    }
}
