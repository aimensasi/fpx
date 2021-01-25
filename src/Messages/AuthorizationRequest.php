<?php

namespace Aimensasi\FPX\Messages;

use Aimensasi\FPX\Constant\Type;
use Aimensasi\FPX\Constants\Response;
use Aimensasi\FPX\Exceptions\InvalidCertificateException;
use Aimensasi\FPX\FPX;
use Aimensasi\FPX\Traits\VerifyCertificate;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Aimensasi\FPX\Contracts\AuthorizationRequestEvent;

class AuthorizationRequest extends FPX {
	use VerifyCertificate;

	/**
	 * Message code on the FPX side
	 */
	public const CODE = 'AR';


	/**
	 * Message Url
	 */
	public $url;


	public function __construct() {
		parent::__construct();

		$this->url = App::environment('production') ?
			Config::get('fpx.urls.production.auth_request') :
			Config::get('fpx.urls.uat.auth_request');
	}

	/**
	 * initiate the payment authorization request
	 *
	 * Options definig the transaction data
	 * @param array $options
	 * @return \Aimensasi\FPX\FPX
	 */
	public function handle($options) {
		$data = Validator::make($options, [
			'flow' => ['required', Rule::in([Type::FLOW_B2C])],
			'reference_id' => 'required',
			'datetime' => 'nullable',
			'currency' => 'nullable',
			'product_description' => 'required',
			'amount' => 'required',
			'customer_name' => 'required',
			'customer_email' => 'required',
			'bank_id' => 'required',
		])->validate();


		$this->type = self::CODE;
		$this->flow = $data['flow'];
		$this->reference = $data['reference_id'];
		$this->timestamp = $data['datetime'] ?? now();
		$this->currency = $data['currency'] ?? $this->currency;
		$this->productDescription = $data['product_description'];
		$this->amount = App::environment('production') ? $data['amount'] : '1.00';
		$this->buyerEmail = $data['customer_email'];
		$this->buyerName = $data['customer_name'];
		$this->targetBankId = $data['bank_id'];
		$this->checkSum = $this->getCheckSum($this->formatRequestData());

		return $this;
	}

	


	public function formatRequestData() {
		$list = collect([
			$this->buyerAccountNumber ?? '',
			$this->targetBankBranch ?? '',
			$this->targetBankId ?? '',
			$this->buyerEmail ?? '',
			$this->buyerIBAN ?? '',
			$this->buyerId ?? '',
			$this->buyerName ?? '',
			$this->buyerMakerName ?? '',
			$this->flow ?? '',
			$this->type ?? '',
			$this->productDescription ?? '',
			$this->bankCode ?? '',
			$this->exchangeId ?? '',
			$this->id ?? '',
			$this->sellerId ?? '',
			$this->reference ?? '',
			$this->timestamp ?? '',
			$this->amount ?? '',
			$this->currency ?? '',
			$this->version ?? '',
		]);

		return $list->join('|');
	}
}
