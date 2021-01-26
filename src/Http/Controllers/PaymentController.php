<?php

namespace Aimensasi\FPX\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Aimensasi\FPX\Messages\AuthorizationRequest;

class PaymentController extends Controller {

	/**
	 * Initiate the request authorization message to FPX
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function handle(Request $request) {
		return view('fpx::redirect_to_bank', [
			'request' => (new AuthorizationRequest)->handle($request->all()),
		]);
	}
}
