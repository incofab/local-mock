<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('events', function (Blueprint $table) {
      $table->id('id');
      $table->string('title');
      $table->text('description')->nullable(true);
      $table->unsignedInteger('duration');
      // $table->text('exam_nos')->nullable(true);
      $table->string('status')->default('active');
      $table->json('event_courses')->nullable();
      $table->dateTime('uploaded_at')->nullable();
      $table->unsignedBigInteger('external_content_id')->nullable();
      $table->json('external_event_courses')->nullable();

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
    Schema::dropIfExists('events');
  }
};
