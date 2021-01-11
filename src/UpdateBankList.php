<?php

namespace Aimensasi\FPX;

use Aimensasi\FPX\Constant\Type;
use Aimensasi\FPX\Models\Bank;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class UpdateBankList {

	public FPX $client;

	public function __construct() {
		$this->client = new FPX();
		$this->client->flow = Type::CODE_BE;
		$this->client->checkSum = $this->getCheckSum();
	}


	public function handle() {
		$dataList = $this->getData();
		$dataStr = $this->stringifyData($dataList);
		try {
			$response = $this->connect($dataList, $dataStr);

			$token = strtok($response, "&");
			$bankList = $this->parseBanksList($token);

			if ($bankList === false) return;

			foreach ($bankList as $key => $status) {
				$bankId = explode(" - ", $key)[1];
				$bank = $this->getBank($bankId);

				Bank::updateOrCreate(['bank_id' => $bankId], [
					'status' => $status == 'A' ? 'Online' : 'Offline',
					'name' => $bank['name'],
					'short_name' => $bank['short_name']
				]);
			}

			return true;
		} catch (\Exception $e) {
			logger("Bank Updating failed", [
				'message' => $e->getMessage(),
			]);
			return false;
		}
	}

	/**
	 * Get request Url
	 *
	 */
	public function getUrl() {
		return App::environment('production') ?
			Config::get('fpx.urls.production.bank_enquiry') :
			Config::get('fpx.urls.uat.bank_enquiry');
	}

	/**
	 * Get CheckSum String
	 *
	 */
	public function getCheckSum() {
		$data = "{$this->client->flow}|{$this->client->bankCode}|{$this->client->exchangeId}|{$this->client->checkSum}";

		$privateKey = openssl_pkey_get_private($this->getPublicKey());
		openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA1);

		return strtoupper(bin2hex($signature));
	}

	/**
	 * Get the certificate public key file
	 *
	 */
	public function getPublicKey() {
		$disk = Config::get('certificates.uat.disk');
		$dir = Config::get('certificates.uat.dir');
		$filename = $this->client->exchangeId . '.key';

		return Storage::disk($disk)->get($dir . '/' . $filename);
	}

	/**
	 * get request data from
	 *
	 */
	public function getData() {
		return collect([
			'fpx_msgToken' => urlencode($this->client->flow),
			'fpx_msgType' => urlencode($this->client->bankCode),
			'fpx_sellerExId' => urlencode($this->client->exchangeId),
			'fpx_version' => urlencode($this->client->version),
			'fpx_checkSum' => urlencode($this->client->checkSum),
		]);
	}

	/**
	 * Convert the data list to string
	 *
	 */
	public function stringifyData($data) {
		$result = '';

		foreach ($data as $key => $value) {
			$result .= $key . '=' . $value . '&';
		}
		rtrim($result, '&');

		return $result;
	}

	/**
	 * connect and excute the request to FPX server
	 *
	 */
	public function connect(Collection $dataList, $dataStr) {
		//open connection
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);

		//set the url, number of POST vars, POST data
		curl_setopt($connection, CURLOPT_URL, $this->getUrl());

		curl_setopt($connection, CURLOPT_POST, $dataList->count());
		curl_setopt($connection, CURLOPT_POSTFIELDS, $dataStr);

		// receive server response ...
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
		//execute post
		$response = curl_exec($connection);
		//close connection
		curl_close($connection);

		return $response;
	}

	/**
	 * Parse the bank list response
	 *
	 */
	public function parseBanksList($response) {
		while ($response !== false) {
			list($key1, $value1) = explode("=", $response);
			$value1 = urldecode($value1);
			$response_value[$key1] = $value1;
			$token = strtok("&");
		}

		$token = strtok($response_value['fpx_bankList'], ",");
		$i = 1;
		while ($token !== false) {
			list($key1, $value1) = explode("~", $token);
			$value1 = urldecode($value1);
			$bankList[$i  . ' - ' . $key1] = $value1;
			$i++;
			$token = strtok(",");
		}

		return $bankList;
	}

	/**
	 * Banks List
	 */
	public function getBank($id) {
		$banks = collect([
			[
				"bank_id" => "ABB0233",
				"status" => "offline",
				"name" => "Affin Bank Berhad",
				"short_name" => "Affin Bank"
			],
			[
				"bank_id" => "ABMB0212",
				"status" => "offline",
				"name" => "Alliance Bank Malaysia Berhad",
				"short_name" => "Alliance Bank (Personal)"
			],
			[
				"bank_id" => "AMBB0209",
				"status" => "offline",
				"name" => "AmBank Malaysia Berhad",
				"short_name" => "AmBank "
			],
			[
				"bank_id" => "BIMB0340",
				"status" => "offline",
				"name" => "Bank Islam Malaysia Berhad",
				"short_name" => "Bank Islam"
			],
			[
				"bank_id" => "BMMB0341",
				"status" => "offline",
				"name" => "Bank Muamalat Malaysia Berhad",
				"short_name" => "Bank Muamalat "
			],
			[
				"bank_id" => "BKRM0602",
				"status" => "offline",
				"name" => "Bank Kerjasama Rakyat Malaysia Berhad ",
				"short_name" => "Bank Rakyat"
			],
			[
				"bank_id" => "BSN0601",
				"status" => "offline",
				"name" => "Bank Simpanan Nasional",
				"short_name" => "BSN"
			],
			[
				"bank_id" => "BCBB0235",
				"status" => "offline",
				"name" => "CIMB Bank Berhad",
				"short_name" => "CIMB Clicks "
			],
			[
				"bank_id" => "CIT0219",
				"status" => "offline",
				"name" => "CITIBANK BHD",
				"short_name" => "Citibank"
			],
			[
				"bank_id" => "HLB0224",
				"status" => "offline",
				"name" => "Hong Leong Bank Berhad",
				"short_name" => "Hong Leong Bank"
			],
			[
				"bank_id" => "HSBC0223",
				"status" => "offline",
				"name" => "HSBC Bank Malaysia Berhad",
				"short_name" => "HSBC Bank"
			],
			[
				"bank_id" => "KFH0346",
				"status" => "offline",
				"name" => "Kuwait Finance House (Malaysia) Berhad",
				"short_name" => "KFH"
			],
			[
				"bank_id" => "MBB0228",
				"status" => "offline",
				"name" => "Malayan Banking Berhad (M2E)",
				"short_name" => "Maybank2E"
			],
			[
				"bank_id" => "MB2U0227",
				"status" => "offline",
				"name" => "Malayan Banking Berhad (M2U)",
				"short_name" => "Maybank2U"
			],
			[
				"bank_id" => "OCBC0229",
				"status" => "offline",
				"name" => "OCBC Bank Malaysia Berhad",
				"short_name" => "OCBC Bank"
			],
			[
				"bank_id" => "PBB0233",
				"status" => "offline",
				"name" => "Public Bank Berhad",
				"short_name" => "Public Bank"
			],
			[
				"bank_id" => "RHB0218",
				"status" => "offline",
				"name" => "RHB Bank Berhad",
				"short_name" => "RHB Bank"
			],
			[
				"bank_id" => "SCB0216",
				"status" => "offline",
				"name" => "Standard Chartered Bank",
				"short_name" => "Standard Chartered"
			],
			[
				"bank_id" => "UOB0226",
				"status" => "offline",
				"name" => "United Overseas Bank",
				"short_name" => "UOB Bank"
			],
		]);

		$banks = $banks->merge($this->getTestingBanks());

		return $banks->firstWhere('bank_id', $id);
	}

	public function getTestingBanks() {
		if (App::environment('production')) {
			return [];
		}
		return [
			[
				"bank_id" => "TEST0021",
				"status" => "offline",
				"name" => "SBI Bank A",
				"short_name" => "SBI Bank A"
			],
			[
				"bank_id" => "TEST0022",
				"status" => "offline",
				"name" => "SBI Bank B",
				"short_name" => "SBI Bank B"
			],
			[
				"bank_id" => "TEST0023",
				"status" => "offline",
				"name" => "SBI Bank C",
				"short_name" => "SBI Bank C "
			],
			[
				"bank_id" => "UOB0229",
				"status" => "offline",
				"name" => "United Overseas Bank - B2C Test",
				"short_name" => "UOB Bank Test ID"
			],
			[
				"bank_id" => "ABB0234",
				"status" => "offline",
				"name" => "Affin Bank Berhad B2C  - Test ID",
				"short_name" => "Affin B2C - Test ID"
			]
		];
	}
}
