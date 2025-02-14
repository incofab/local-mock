<?php

use App\Models\Exam;
use App\Helpers\ExamHandler;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\postJson;

beforeEach(function () {
  $this->examNo = 'test123';
  $examHandler = ExamHandler::make();
  $this->filename = $examHandler->getFullFilepath($this->examNo);

  $this->exam = Exam::factory()
    ->notStarted()
    ->create([
      'exam_no' => $this->examNo,
    ]);
});

afterEach(function () {
  if (file_exists($this->filename)) {
    File::delete($this->filename);
  }
});

it('starts exam successfully with valid data', function () {
  postJson(route('api.start-exam'), [
    'exam_no' => $this->examNo,
  ])
    ->assertStatus(200)
    ->assertJsonStructure([
      'data' => ['exam_track', 'exam', 'timeRemaining', 'baseUrl'],
    ]);
});

it('fails when exam is not found', function () {
  postJson(route('api.start-exam'), [
    'exam_no' => 'INVALID_EXAM',
  ])
    ->assertStatus(422)
    ->assertJsonValidationErrors(['exam_no']);
});

it('handles exam startup failure', function () {
  $this->exam->markAsEnded(10, 20);
  postJson(route('api.start-exam'), [
    'exam_no' => $this->examNo,
  ])
    ->assertStatus(401)
    ->assertJsonFragment(['message' => 'Exam has already ended']);
});
