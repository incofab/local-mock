<?php

use App\Enums\ExamStatus;
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
    Schema::create('exams', function (Blueprint $table) {
      $table->id();
      $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
      $table->string('exam_no')->unique();
      $table->unsignedBigInteger('student_id')->nullable();

      // $table->unsignedInteger('duration');
      $table->float('time_remaining');

      $table->dateTime('start_time')->nullable(true);
      $table->dateTime('pause_time')->nullable(true);
      $table->dateTime('end_time')->nullable(true);
      $table->unsignedInteger('score')->nullable(true);
      $table->unsignedInteger('num_of_questions')->nullable(true);
      $table->string('status')->default(ExamStatus::Pending->value);
      $table->json('meta')->nullable();
      $table->json('attempts')->nullable();
      $table->json('student')->nullable();
      $table->json('exam_courses')->nullable();

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
    Schema::dropIfExists('exams');
  }
};
