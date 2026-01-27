<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPasswordCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:reset-password {email} {password}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $email = $this->argument('email');
    $password = $this->argument('password');
    $users =
      trim($email) == 'all'
        ? User::query()->take(50)->get()
        : User::where('email', $email)->get();
    if ($users->isEmpty()) {
      $this->error('User not found');
      return;
    }
    foreach ($users as $user) {
      $user->password = Hash::make($password);
      $user->save();
    }
    $this->info('Password reset successfully');
  }
}
