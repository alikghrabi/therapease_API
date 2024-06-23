<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTherapistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('therapists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->string('verification_code')->nullable();
            $table->enum('application_status', ['pending', 'accepted'])->default('pending');
            $table->string('cv_file_path')->nullable();
            $table->integer('experience')->nullable();
            $table->text('description_profile')->nullable();
            $table->text('description_registration')->nullable();
            $table->string('id_front_pic');
            $table->string('id_back_pic');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('therapists');
    }
}
