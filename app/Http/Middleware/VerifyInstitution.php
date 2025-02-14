<?php

namespace App\Http\Middleware;

use App\Actions\InstitutionHandler;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class VerifyInstitution
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $institutionHandler = InstitutionHandler::getInstance();
    if (!$institutionHandler->isRecorded()) {
      return redirect(route('register'))->with(
        'error',
        'You need to set up your institution'
      );
    }

    $institution = $institutionHandler->getInstitution();
    abort_unless($institution, 401, 'Institution data not available');

    if ($request->method() === 'GET') {
      View::share('institution', $institution);
    }

    return $next($request);
  }
}
