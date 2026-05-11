<?php

namespace App\Actions;

use App\Models\Institution;
use App\Support\LocalApiFixture;
use App\Support\Platform\PlatformUrl;
use App\Support\Res;

class InstitutionHandler
{
  private static $instance;

  private ?Institution $institution = null;

  private $filename;

  public function __construct()
  {
    $this->filename = public_path('institution-data.json');
  }

  public static function getInstance(): static
  {
    if (!self::$instance) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function save($data)
  {
    file_put_contents($this->filename, json_encode($data, JSON_PRETTY_PRINT));
    $this->institution = new Institution($data);
  }

  /** Only called when resetting the app */
  public function deleteFile()
  {
    if (file_exists($this->filename)) {
      unlink($this->filename);
    }
    $this->institution = null;
  }

  public function isRecorded(): bool
  {
    return file_exists($this->filename);
  }

  public function getInstitution(): ?Institution
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

  public function processInstitutionCode($code, $platform): Res
  {
    $fixture = LocalApiFixture::make();
    if ($fixture?->matchesInstitution($code, $platform)) {
      $institution = $fixture->institution();
      $this->save([...$institution, 'platform' => $platform]);

      return successRes('Data recorded successfully');
    }

    $url = PlatformUrl::make($platform, $code)->showInstitution();
    $res = http()->post($url);
    // dd($res->json(), $url);
    if (!$res->json('success', false)) {
      return failRes('Error processing request');
    }
    $institution = $res->json('data');
    $this->save([...$institution, 'platform' => $platform]);

    return successRes('Data recorded successfully');
  }
}
