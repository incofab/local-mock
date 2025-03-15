<?php

use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Exam;
use App\Support\ExamProcess;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
  Storage::fake();
  $this->examNo = '1234';
  // $this->filename = ExamHandler::make()->getFullFilepath($this->examNo);
  $this->testFilePath = Storage::path('exam_test_file.json');
  $this->realTestFilePath = ExamHandler::make()->getFullFilepath($this->examNo);
  $this->examFileContent = [
    'exam' => [
      ...Exam::factory()
        ->started()
        ->make(['exam_no' => $this->examNo])
        ->toArray(),
    ],
    'attempts' => ['1' => 'A', '2' => 'C'],
  ];
  file_put_contents(
    $this->realTestFilePath,
    json_encode($this->examFileContent)
  );
  // $this->realTestFilePath = public_path('exams/test.json');
});
afterEach(function () {
  @unlink($this->realTestFilePath);
});

// Test the syncExamFile method
it('creates or updates an exam file for starting an exam', function () {
  // Arrange: Create a mock Exam object
  $exam = Exam::factory()
    ->notStarted()
    ->make(['exam_no' => $this->examNo]);

  $handler = Mockery::mock(ExamHandler::class)->makePartial();
  $handler
    ->shouldReceive('getExamTrack')
    ->with($exam->exam_no, true)
    ->andReturn(
      ExamProcess::success()
        ->examTrack([])
        ->file($this->testFilePath)
    );
  /** @var ExamHandler $handler */
  $result = $handler->syncExamFile($exam, true);

  // Assert: Check that the result is successful
  expect($result->isSuccessful())
    ->toBeTrue()
    ->and($result->getMessage())
    ->toBe('Exam file ready');
});

// Test the attemptQuestion method
it('records a student attempt in the exam file', function () {
  // $examNo = 'exam123';
  $studentAttempts = [
    'question1' => 'answer1',
    'question2' => 'answer2',
  ];

  $examFileContent = [
    'exam' => ['status' => ExamStatus::Active->value],
    'attempts' => [],
  ];

  $handler = Mockery::mock(ExamHandler::class)->makePartial();
  $handler
    ->shouldReceive('getExamTrack')
    ->with($this->examNo)
    ->andReturn(
      ExamProcess::success()
        ->examTrack($examFileContent)
        ->file($this->testFilePath)
    );

  $handler
    ->shouldReceive('saveFile')
    ->with(
      $this->testFilePath,
      Mockery::on(function ($content) use ($studentAttempts) {
        return isset($content['attempts']['question1']) &&
          $content['attempts']['question1'] === 'answer1';
      })
    )
    ->andReturnTrue();

  /** @var ExamHandler $handler */
  $result = $handler->attemptQuestion($studentAttempts, $this->examNo);

  expect($result->isSuccessful())
    ->toBeTrue()
    ->and($result->getMessage())
    ->toBe('Attempt recorded');
});

// Test the endExam method
it('marks the exam as ended', function () {
  $handler = Mockery::mock(ExamHandler::class)->makePartial();
  $handler
    ->shouldReceive('getExamTrack')
    ->with($this->examNo, false)
    ->andReturn(
      ExamProcess::success()
        ->examTrack($this->examFileContent)
        ->file($this->testFilePath)
    );

  $handler
    ->shouldReceive('saveFile')
    ->with(
      $this->testFilePath,
      Mockery::on(function ($content) {
        return $content['exam']['status'] === ExamStatus::Ended->value;
      })
    )
    ->andReturnTrue();

  /** @var ExamHandler $handler */
  $result = $handler->endExam($this->examNo);

  expect($result->isSuccessful())
    ->toBeTrue()
    ->and($result->getMessage())
    ->toBe('Exam ended');
});

it('returns time elapsed if exam time is over', function () {
  $exam = Exam::factory()->make([
    'end_time' => now()->subMinutes(30)->toDateTimeString(),
  ]);
  $handler = Mockery::mock(ExamHandler::class)->makePartial();
  $handler
    ->shouldReceive('getFullFilepath')
    ->with($exam->exam_no)
    ->andReturn($this->realTestFilePath);
  $handler
    ->shouldReceive('getExamTrack')
    ->with($this->realTestFilePath)
    ->andReturn(['exam' => $exam->toArray()]);

  /** @var ExamHandler $handler */
  $result = $handler->getContent($exam->exam_no);

  expect($result->isNotSuccessful())
    ->toBeTrue()
    ->and($result->getMessage())
    ->toBe('Time Elapsed/Exam ended');
});

it('returns successful exam content if all conditions are met', function () {
  $exam = Exam::factory()->make([
    'end_time' => now()->addMinutes(30)->toDateTimeString(),
  ]);
  $handler = Mockery::mock(ExamHandler::class)->makePartial();
  $handler
    ->shouldReceive('getFullFilepath')
    ->with($exam->exam_no)
    ->andReturn($this->realTestFilePath);
  $handler
    ->shouldReceive('getExamTrack')
    ->with($this->realTestFilePath)
    ->andReturn(['exam' => $exam->toArray()]);
  /** @var ExamHandler $handler */
  $result = $handler->getContent($exam->exam_no);
  // dd($exam->toArray());
  // dd($result->toArray());
  expect($result->isSuccessful())
    ->toBeTrue()
    ->and($result->getMessage())
    ->toBe('')
    ->and($result->hasExamTrack())
    ->toBeTrue();
});
