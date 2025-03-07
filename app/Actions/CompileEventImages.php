<?php
namespace App\Actions;

use App\Models\CourseSession;
use App\Models\Event;
use App\Models\EventCourse;

class CompileEventImages
{
  function __construct(private Event $event, private string $imagesOutputFolder)
  {
  }

  static function make(Event $event, string $imagesOutputFolder)
  {
    return new self($event, $imagesOutputFolder);
  }

  function run()
  {
    $this->event->getEventCourses()->each(function (EventCourse $eventCourse) {
      $courseSession = $eventCourse->course_session;

      $questionHtml = $this->getQuestionHtml($courseSession);
      $srcAttributes = $this->getImageSrcAttributes($questionHtml, false, true);
      $this->compileImages($srcAttributes, $courseSession);
    });
  }

  private function getQuestionHtml(CourseSession $courseSession)
  {
    $html = '';
    foreach ($courseSession->questions as $key => $question) {
      $html .= "<div>{$question->question} {$question->option_a} {$question->option_b} {$question->option_c} {$question->option_d} {$question->option_e} {$question->answer_meta}</div>";
    }
    return $html;
  }

  private function compileImages(
    array $imageSrcAttributes,
    CourseSession $courseSession
  ) {
    foreach ($imageSrcAttributes as $imageSrc) {
      $filename = "session_{$courseSession->id}_" . basename($imageSrc);
      $this->downloadImage($imageSrc, $this->imagesOutputFolder . $filename);
    }
  }

  private function downloadImage($imageUrl, $savePath)
  {
    $imageData = @file_get_contents($imageUrl);
    if ($imageData === false) {
      return false;
    }
    return file_put_contents($savePath, $imageData) !== false;
  }

  private function getImageSrcAttributes(
    $html,
    $basenameOnly = true,
    $skipEmbeddedImages = false
  ): array {
    if (empty($html)) {
      return [];
    }

    libxml_use_internal_errors(true);
    $dom = new \DOMDocument();
    $dom->loadHTML($html);

    $images = $dom->getElementsByTagName('img');

    $imageSrcAttributes = [];
    /** @var \DOMElement $image */
    foreach ($images as $image) {
      $src = $image->getAttribute('src');
      if (empty($src)) {
        continue;
      }
      if ($skipEmbeddedImages && str_contains($src, 'data:')) {
        continue;
      }
      $imageSrcAttributes[] = $basenameOnly ? basename($src) : $src;
    }

    return $imageSrcAttributes;
  }
}
