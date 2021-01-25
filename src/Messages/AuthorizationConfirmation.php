<?php 

namespace Aimensasi\FPX\Messages;

use Aimensasi\FPX\FPX;
use Aimensasi\FPX\Constant\Response;
use Aimensasi\FPX\Exceptions\InvalidCertificateException;

class AuthorizationConfirmation extends FPX{


  /**
	 * Message code on the FPX side
	 */
  public const CODE = 'AC';

  public const STATUS_SUCCESS = 'succeeded';
  public const STATUS_FAILED = 'failed';

  public const SUCCESS_CODE = '00';
  


  /**
	 * Handle the direct response from the FPX server
	 *
   * @param array $options
   * @return \Aimensasi\FPX\FPX
	 */
	public function handle($options) {
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
		$this->flow = $options['fpx_msgToken'];
		$this->type = $options['fpx_msgType'];
		$this->exchangeId = $options['fpx_sellerExId'];
		$this->id = $options['fpx_sellerExOrderNo'];
		$this->sellerId = $options['fpx_sellerId'];
		$this->reference = $options['fpx_sellerOrderNo'];
		$this->timestamp = $options['fpx_sellerTxnTime'];
		$this->amount = $options['fpx_txnAmount'];
		$this->currency = $options['fpx_txnCurrency'];
    $this->checkSum = $options['fpx_checkSum'];

    try {
			$this->verifySign($this->checkSum, $this->formatData());

			if ($this->debitResponseStatus == self::SUCCESS_CODE) {
				return [
          'status' => self::STATUS_SUCCESS,
          'message' => 'Payment is successfull',
          'transaction_id' => $this->foreignId,
          'reference_id' => $this->reference,
        ];
			}

      return [
        'status' => self::STATUS_FAILED,
        'message' => Response::STATUS[$this->debitResponseStatus] ?? 'Payment Request Failed',
        'transaction_id' => $this->foreignId,
        'reference_id' => $this->reference,
      ];
		} catch (InvalidCertificateException $e) {
			return [
        'status' => self::STATUS_FAILED,
        'message' => "Failed to verifiy the request origin",
        'transaction_id' => $this->foreignId,
        'reference_id' => $this->reference,
      ];
		}
	}

  public function formatData() {
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
			$this->flow ?? '',
			$this->type ?? '',
			$this->exchangeId ?? '',
			$this->id ?? '',
			$this->sellerId ?? '',
			$this->reference ?? '',
			$this->timestamp ?? '',
			$this->amount ?? '',
			$this->currency ?? '',
		]);
      
		return $list->join('|');
	}
}