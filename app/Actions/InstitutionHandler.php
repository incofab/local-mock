<?php
namespace App\Actions;

use App\Models\Institution;
use App\Support\Res;
use App\Support\WebsiteHelper;
use Illuminate\Support\Facades\Http;

class InstitutionHandler
{
  private static $instance;

  private ?Institution $institution = null;
  private $filename;

  function __construct()
  {
    $this->filename = public_path('institution-data.json');
  }

  static function getInstance(): static
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  function save($data)
  {
    file_put_contents($this->filename, json_encode($data, JSON_PRETTY_PRINT));
  }

  function isRecorded(): bool
  {
    return file_exists($this->filename);
  }

  function getInstitution(): Institution|null
  {
    if ($this->institution) {
      return $this->institution;
    }

    if (!$this->isRecorded()) {
      return null;
    }

    $data = json_decode(file_get_contents($this->filename), true);
    if (!$data) {
      return null;
    }

    $this->institution = new Institution($data);
    return $this->institution;
  }

  function processInstitutionCode($code): Res
  {
    $res = http()->post(WebsiteHelper::showInstitutionUrl($code));
    if (!$res->json('success', false)) {
      return failRes('Error processing request');
    }
    $institution = $res->json('data');
    $this->save($institution);
    return successRes('Data recorded successfully');
  }
}
