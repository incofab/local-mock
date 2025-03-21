<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
  use AuthorizesRequests, ValidatesRequests;
  function ok($data = [], $message = '')
  {
    return response()->json([
      'message' => $message,
      'success' => true,
      'data' => $data,
    ]);
  }

  function fail($data = [], $message = '')
  {
    return response()->json(
      [
        'message' => $message,
        'success' => false,
        'data' => $data,
      ],
      401
    );
  }
}
