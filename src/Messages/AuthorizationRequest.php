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
	public const RESPONSE_DIRECT_AC = 'Direct AC';
	public const RESPONSE_INDIRCT_AC = 'Indirect AC';


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
	 * The type of message
	 * @see Aimensasi\FPX\Constant\Type;
	 * @param string $type
	 *
	 * @see Aimensasi\FPX\Constant\Type;
	 * @param string $flow
	 *
	 * Options definig the transaction data
	 * @param array $options
	 */
	public function handleRequest($options) {
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
		$this->amount = $data['amount'];
		$this->buyerEmail = $data['customer_email'];
		$this->buyerName = $data['customer_name'];
		$this->targetBankId = $data['bank_id'];
		$this->checkSum = $this->getCheckSum($this->formatRequestData());

		return $this;
	}

	/**
	 * Handle the direct response from the FPX server
	 *
	 *
	 */
	public function handleDirectResponse($options) {
		$this->targetBankBranch = $options['fpx_buyerBankBranch'];
		$this->targetBankId = $options['fpx_buyerBankId'];
		$this->buyerIBAN = $options['fpx_buyerIban'];
		$this->buyerId = $options['fpx_buyerId'];
		$this->buyerName = $options['fpx_buyerName'];
		$this->creditResponseStatus = $options['fpx_creditAuthCode'];
		$this->creditResponseNumber = $options['fpx_creditAuthNo'];
		$this->debitResponseStatus = $options['fpx_debitAuthCode'];
		$this->debitResponseNumber = $options['fpx_debitAuthNo'];
		$this->foreignId = $options['fpx_fpxTxnId'];
		$this->foreignTimestamp = $options['fpx_fpxTxnTime'];
		$this->makerName = $options['fpx_makerName'];
		$this->type = self::RESPONSE_DIRECT_AC;
		$this->flow = $options['fpx_msgType'];
		$this->exchangeId = $options['fpx_sellerExId'];
		$this->id = $options['fpx_sellerExOrderNo'];
		$this->sellerId = $options['fpx_sellerId'];
		$this->reference = $options['fpx_sellerOrderNo'];
		$this->timestamp = $options['fpx_sellerTxnTime'];
		$this->amount = $options['fpx_txnAmount'];
		$this->currency = $options['fpx_txnCurrency'];
		$this->checkSum = $options['fpx_checkSum'];

		$event = new AuthorizationRequestEvent;

		try {
			$this->verifySign($this->checkSum, $this->formatResponseData());

			if ($this->debitResponseStatus == Response::STATUS_OK) {

				$event->pass($this, [
					'transaction_id' => $this->id,
					'bank_transaction_id' => $this->foreignId,
					'reference_id' => $this->reference,
					'amount' => $this->amount,
				]);
			} else {
				$event->fail($this, [
					'transaction_id' => $this->id,
					'bank_transaction_id' => $this->foreignId,
					'reference_id' => $this->reference,
					'amount' => $this->amount,
					'response' => $this->debitResponseNumber,
				]);
			}

			// call action that can update the status of the object invoice, subscription or whatever
		} catch (InvalidCertificateException $e) {
			$event->fail($this, [
				'transaction_id' => $this->id,
				'bank_transaction_id' => $this->foreignId,
				'reference_id' => $this->reference,
				'amount' => $this->amount,
				'response' => 'Fail to verify response origin',
			]);
		}
	}



	public function formatRequestData() {
		$list = collect([
			$this->buyerAccountNumber ?? '',
			$this->targetBankId ?? '',
			$this->buyerEmail ?? '',
			$this->buyerIBAN ?? '',
			$this->buyerId ?? '',
			$this->buyerName ?? '',
			$this->flow ?? '',
			$this->type ?? '',
			$this->productDescription ?? '',
			$this->bankCode ?? '',
			$this->exchangeId ?? '',
			$this->id ?? '',
			$this->sellerId ?? '',
			$this->reference ?? '',
			$this->datetime ?? '',
			$this->amount ?? '',
			$this->currency ?? '',
			$this->version ?? '',
		]);

		return $list->join('|');
	}

	public function formatResponseData() {
		$list = collect([
			$this->targetBankBranch ?? '',
			$this->targetBankId ?? '',
			$this->buyerIBAN ?? '',
			$this->buyerId ?? '',
			$this->buyerName ?? '',
			$this->creditResponseStatus ?? '',
			$this->creditResponseNumber ?? '',
			$this->debitResponseStatus ?? '',
			$this->debitResponseNumber ?? '',
			$this->foreignId ?? '',
			$this->foreignTimestamp ?? '',
			$this->makerName ?? '',
			$this->type ?? '',
			$this->flow ?? '',
			$this->exchangeId ?? '',
			$this->id ?? '',
			$this->sellerId ?? '',
			$this->reference ?? '',
			$this->timestamp ?? '',
			$this->amount ?? '',
			$this->currency ?? '',
			$this->checkSum ?? '',
		]);

		return $list->join('|');
	}
}
