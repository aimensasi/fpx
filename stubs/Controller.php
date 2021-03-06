<?php

namespace App\Http\Controllers\FPX;

use Aimensasi\FPX\Http\Requests\AuthorizationConfirmation as Request;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController {


	/**
	 * @param Request $request
	 * @return Response
	 */
	public function callback(Request $request) {
		$response = $request->handle();

		// Update your order status
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function webhook(Request $request) {
		$response = $request->handle();

		// Update your order status

		return 'OK';
	}
}
