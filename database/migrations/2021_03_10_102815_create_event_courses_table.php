<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/** @deprecated */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    return;
    Schema::create('event_courses', function (Blueprint $table) {
      $table->id();
      $table->foreignId('event_id')->constrained('events');
      $table->unsignedBigInteger('course_session_id');
      $table->string('status')->default('active');
      $table->unsignedInteger('num_of_questions')->nullable(true);
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
    Schema::dropIfExists('event_courses');
  }
};
