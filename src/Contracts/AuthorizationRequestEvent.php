<?php

namespace Aimensasi\FPX\Contracts;

use Aimensasi\FPX\Messages\AuthorizationRequest;

interface AuthorizationRequestEvent {

	/**
	 * handle the authorization passing event.
	 *
	 * you can use this method to update your subscription or invoice status
	 *
	 * @param AuthorizationRequest $request
	 * @param  array  $extra
	 * @return void
	 */
	public function pass(AuthorizationRequest $request, $extra);


	/**
	 * handle the authorization passing event.
	 *
	 * you can use this method to update your subscription or invoice status
	 *
	 * @param AuthorizationRequest $request
	 * @param  array  $extra
	 * @return void
	 */
	public function fail(AuthorizationRequest $request, $extra);
}
