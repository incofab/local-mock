<?php

namespace App\Http\Controllers\Auth;

use App\Actions\InstitutionHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
  function create()
  {
    if (!InstitutionHandler::getInstance()->isRecorded()) {
      return redirect(route('register'));
    }
    return view('auth.login');
  }

  function store(Request $request)
  {
    $data = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);

    if (Auth::attempt($data, $request->filled('remember'))) {
      $request->session()->regenerate();
      return redirect()->intended(route('admin.dashboard'));
    }

    throw ValidationException::withMessages([
      'email' => ['The provided credentials do not match our records.'],
    ]);
  }
  public function logout(Request $request)
  {
    Auth::logout();

    // Regenerate the session to prevent session fixation
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect(route('login'))->with(
      'message',
      'You have been logged out.'
    );
  }
}
