<?php

namespace Aimensasi\FPX\Controllers;

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
	public function requestAuthorization(Request $request) {
		return view('fpx::redirect_to_bank', [
			'request' => (new AuthorizationRequest)->handleRequest($request->all()),
		]);
	}

	/**
	 * Handle the direct message callback from FPX
	 *
	 * @param Request $request
	 * @return string
	 */
	public function directCallback(Request $request) {
		$type = $request->fpx_msgToken;

		switch ($type) {
			case AuthorizationRequest::RESPONSE_DIRECT_AC:
				(new AuthorizationRequest)->handleDirectResponse($request->all());
				return 'OK';
				break;
		}
	}

	/**
	 * Handle the indirect message callback from FPX
	 *
	 * @param Request $request
	 * @return string
	 */
	public function indirectCallback(Request $request) {
		# code...
	}
}
