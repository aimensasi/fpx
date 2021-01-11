<?php

namespace App\Actions\FPX;

use Aimensasi\FPX\Contracts\AuthorizationRequestEvent as BaseEvent;
use Aimensasi\FPX\Messages\AuthorizationRequest;

class AuthorizationRequestEvent implements BaseEvent {

	/**
	 * handle the authorization passing event.
	 *
	 * you can use this method to update your subscription or invoice status
	 *
	 * @param AuthorizationRequest $request
	 * @param  array  $extra
	 * @return void
	 */
	public function pass(AuthorizationRequest $request, $extra) {
		// code
	}


	/**
	 * handle the authorization passing event.
	 *
	 * you can use this method to update your subscription or invoice status
	 *
	 * @param AuthorizationRequest $request
	 * @param  array  $extra
	 * @return void
	 */
	public function fail(AuthorizationRequest $request, $extra) {
		// code
	}
}
