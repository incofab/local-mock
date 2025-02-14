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
    Schema::create('exam_courses', function (Blueprint $table) {
      $table->id();
      $table->foreignId('exam_id')->references('id')->on('exams');
      $table->unsignedBigInteger('course_session_id');
      $table->unsignedInteger('score')->nullable(true);
      $table->unsignedInteger('num_of_questions')->nullable(true);
      $table->string('status')->default('active');
      $table->string('course_code');
      $table->string('session')->nullable();
      $table->text('meta')->nullable();
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
    return;
    Schema::dropIfExists('exam_courses');
  }
};
