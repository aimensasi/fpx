<?php

namespace Aimensasi\FPX\Http\Requests;

use Aimensasi\FPX\Messages\AuthorizationConfirmation as AuthorizationConfirmationMessage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AuthorizationConfirmation extends FormRequest {
  
  /**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [

    ];
	}


	/**
	 * Presist the data to the users table
	 */
	public function handle() {
		$data = $this->all();
		$request = new AuthorizationConfirmationMessage;

		$response = $request->handle($data);
	
		return $response;
	}
}
