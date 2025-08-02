<?php

namespace App\Traits;

trait Res
{
  public function sendRes($message, $status = true,  $data = [], $errors = [],$code=200)
  {
    return response()->json([
      'status' => $status,
      'message' => $message,
      'data' => $data,
      'errors' => $errors,
    ],$code);
  }

}
