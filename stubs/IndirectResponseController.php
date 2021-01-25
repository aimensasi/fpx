<?php

namespace App\Http\Controllers\FPX;

use App\Http\Controllers\Controller;

class IndirectResponseController extends Controller{

  public function handle(AuthorizationRequest $request){
    $request->handle();
  }
}