<?php

namespace App\Console\Commands;

use App\Actions\InstitutionHandler;
use Illuminate\Console\Command;

class ResetAppCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:reset';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Reset the application';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $this->call('migrate:fresh');
    // $this->call('db:seed');
    InstitutionHandler::getInstance()->deleteFile();
    $this->info('Application reset successfully');
  }
}
