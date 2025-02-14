<?php

namespace App\Http\Controllers\Auth;

use App\Actions\InstitutionHandler;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
  function create()
  {
    return view('auth.register');
  }

  /**
   * Create a new user instance after a valid registration.
   *
   * @param array $data
   * @return \App\Models\User
   */
  protected function store(Request $request)
  {
    $data = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
      'code' => ['required', 'string'],
    ]);

    if (
      InstitutionHandler::getInstance()
        ->processInstitutionCode($request->code)
        ->isNotSuccessful()
    ) {
      throw ValidationException::withMessages([
        'code' => 'Error processing institution code',
      ]);
    }

    $user = User::create([
      ...collect($data)->except('code')->toArray(),
      'password' => Hash::make($data['password']),
    ]);
    Auth::login($user);
    return redirect(route('admin.dashboard'));
  }
}
