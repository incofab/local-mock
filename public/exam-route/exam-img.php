<?php
require_once 'exam-route-base.php';

$courseId = $_REQUEST['course_id'] ?? null;
$courseSessionId = $_REQUEST['course_session_id'] ?? null;
$filename = $_REQUEST['filename'] ?? null;
$session = $_REQUEST['session'] ?? null;
$eventId = $_REQUEST['event_id'] ?? null;

$file =
  __DIR__ .
  "/../content/event_$eventId/images/session_{$courseSessionId}_$filename";

if (!file_exists($file)) {
  return null;
}

$type = 'image/jpeg';
header('Content-Type:' . $type);
header('Content-Length: ' . filesize($file));
readfile($file);
