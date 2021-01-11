<?php

namespace App\View\Components;

use Aimensasi\FPX\Models\Bank;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class PayComponent extends Component {

	public $referenceId;
	public $datetime;
	public $currency;
	public $productDescription;
	public $amount;
	public $customerName;
	public $customerEmail;
	public $selectedBankId;

	public $flow;
	public $type;
	public Collection $banks;

	public function __construct() {
		$this->banks = Bank::orderBy('name')->all();
	}

	/**
	 * Get the view / contents that represents the component.
	 *
	 * @return \Illuminate\View\View
	 */
	public function render() {
		return view('fpx::components.pay');
	}
}
