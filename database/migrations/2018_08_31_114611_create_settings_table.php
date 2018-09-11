<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->integer('club_id');
            $table->text('club_desc')->nullable();
            $table->boolean('master')->default(1); //Allow Timepunches (Admin can always punch)
            $table->boolean('allow_mark')->default(1); //Allow Mark for Review
            $table->boolean('allow_delete')->default(0); //Allow Delete Timepunch from My Hours Page
            $table->boolean('allow_comments')->default(1);

            $table->primary('club_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
