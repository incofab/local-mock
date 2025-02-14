<?php
namespace App\Actions;

use App\Support\WebsiteHelper;
use Illuminate\Support\Facades\Http;

class FakeData
{
  function __construct()
  {
  }

  static function make()
  {
    return new self();
  }

  function run()
  {
    $this->registerInstitutionFaker()
      ->listEvents()
      ->showEvent()
      ->showDeepEvent()
      ->listExams();
  }

  function registerInstitutionFaker()
  {
    $url =
      WebsiteHelper::make()->getBaseUrl() . 'institutions/*/show-institution';
    Http::fake([
      $url => Http::response([
        'success' => true,
        'data' => json_decode(
          file_get_contents(public_path('sample-data/sample-institution.json')),
          true
        ),
      ]),
    ]);
    return $this;
  }

  private function listEvents()
  {
    $url = WebsiteHelper::make()->url(WebsiteHelper::LIST_EVENTS);
    $url2 = "$url?latest_event_id=*";
    $res = Http::response([
      'success' => true,
      'data' => json_decode(
        file_get_contents(public_path('sample-data/list-events.json')),
        true
      ),
    ]);
    Http::fake([
      $url => $res,
      $url2 => $res,
    ]);
    return $this;
  }

  private function showEvent()
  {
    $url = WebsiteHelper::make()->url(WebsiteHelper::SHOW_EVENT, [
      'event' => '*',
    ]);
    Http::fake([
      $url => Http::response([
        'success' => true,
        'data' => json_decode(
          file_get_contents(public_path('sample-data/event.json')),
          true
        ),
      ]),
    ]);
    return $this;
  }

  private function showDeepEvent()
  {
    $url = WebsiteHelper::make()->url(WebsiteHelper::SHOW_DEEP_EVENT, [
      'event' => '*',
    ]);
    Http::fake([
      $url => Http::response([
        'success' => true,
        'data' => json_decode(
          file_get_contents(public_path('sample-data/deep-event.json')),
          true
        ),
      ]),
    ]);
    return $this;
  }

  private function listExams()
  {
    $url = WebsiteHelper::make()->url(WebsiteHelper::LIST_EVENT_EXAMS, [
      'event' => '*',
    ]);
    Http::fake([
      $url => Http::response([
        'success' => true,
        'data' => json_decode(
          file_get_contents(public_path('sample-data/list-exams.json')),
          true
        ),
      ]),
    ]);
    return $this;
  }
}
