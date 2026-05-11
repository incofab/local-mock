<?php
require_once 'exam-route-base.php';

$examHandler = new \App\Helpers\ExamHandler();

$input = @file_get_contents('php://input');
$post = json_decode($input, true);
$eventId = $post['event_id'] ?? null;
$examNo = $post['exam_no'] ?? null;

// dlog($post);
$allAttempts = $post['attempts'] ?? [];
$theoryAttempts = $post['theory_attempts'] ?? [];

if (!is_array($allAttempts)) {
  $allAttempts = [];
}

if (is_array($theoryAttempts)) {
  $allAttempts = array_merge($allAttempts, $theoryAttempts);
}

if (!$examNo) {
  emitResponse([
    'success' => false,
    'message' => 'Exam number is required',
  ]);
  exit();
}

$ret = $examHandler->attemptQuestion($allAttempts, $examNo);

if ($ret->isNotSuccessful()) {
  emitResponse($ret);
}

emitResponse([
  'success' => true,
  'data' => ['success' => array_values($allAttempts), 'failure' => []],
]);
